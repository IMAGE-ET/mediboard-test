<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
$chrono = new Chronometer;

set_time_limit( 1800 );
ignore_user_abort( 1 );

$step = 100;

$current = mbGetValueFromGet("current", 0);
$new = mbGetValueFromGet("new", 0);
$link = mbGetValueFromGet("link", 0);
$nofile = mbGetValueFromGet("nofile", 0);
$noconsult = mbGetValueFromGet("noconsult", 0);
$totalSize = mbGetValueFromGet("totalSize", 0);
$total = mbGetValueFromGet("total", 0);

$sql = "SELECT * FROM import_courriers" .
      "\nLIMIT ".($current*$step).", $step";
$listImport = $ds->loadlist($sql);


foreach($listImport as $key => $value) {
  
  $chrono->start();
  
  $sql = "SELECT * FROM files_mediboard" .
      "\nWHERE files_mediboard.file_name = '".addslashes(trim($value["nom"]))."'";
  $match = $ds->loadlist($sql);
  //echo "$total : Cas de ".$value["nom"]." :<br>";
  if(!count($match)) {
    $file = new CFile;
    // DB Table key
    $file_id = '';
    // DB Fields
    $file->file_name = trim($value["nom"]);
    $file->file_date = mbDateTime();
    $file->file_real_filename = uniqid( rand() );
    if(strpos($file->file_name, ".txt")) {
      $file->file_type = "text/plain";
      //$file->file_name = strtoupper($file->file_name);
    }
    elseif(strpos($file->file_name, ".doc")) {
      $file->file_type = "application/msword";
      $file->file_name = str_replace(".doc", ".DOC", $file->file_name);
    }
    else {
      $file->file_type = "application/octet-stream";
      //$file->file_name = strtoupper($file->file_name);
    }
    $sql = "SELECT *" .
        "\nFROM consultation, plageconsult, import_patients, users_mediboard" .
        "\nWHERE consultation.patient_id = import_patients.mb_id" .
        "\nAND import_patients.patient_id = '".$value["pat_id"]."'" .
        "\nAND consultation.plageconsult_id = plageconsult.plageconsult_id" .
        "\nAND plageconsult.chir_id = users_mediboard.user_id" .
        "\nAND users_mediboard.function_id = '11'" .
        "\nORDER by plageconsult.date DESC, plageconsult.debut DESC";
    $result = $ds->loadlist($sql);
    if(!count($result))
      $noconsult++;
    elseif(!file_exists("modules/dPinterop/courriers/".$file->file_name)) {
      $nofile++;
      echo "modules/dPinterop/courriers/".$file->file_name."<br>";
    }
    else {
      $consult = $result[0];
      $file->file_size = filesize("modules/dPinterop/courriers/".$file->file_name);
      $file->object_class = "CConsultation";
      $file->object_id = $consult["consultation_id"];
      $file->store();
      $file->moveTemp("modules/dPinterop/courriers/".$file->file_name);
			
      $sql = "UPDATE import_courriers" .
            "\nSET mb_id = '".$file->file_id."'" .
            "\nWHERE nom = '".$value["nom"]."'";
      $ds->exec($sql);

      $new++;
      $totalSize += $file->file_size;
    }
  } else {
    $sql = "UPDATE import_courriers" .
        "\nSET mb_id = '".$match[0]["file_id"]."'" .
        "\nWHERE nom = '".$value["nom"]."'";
    $ds->exec($sql);
    $link++;
  }
  $total++;
  $chrono->stop();
}
$current++;

$bytes = $totalSize;
$value = $bytes;
$unit = "o";

$kbytes = $bytes / 1024;
if ($kbytes >= 1) {
  $value = $kbytes;
  $unit = "Ko";
}

$mbytes = $kbytes / 1024;
if ($mbytes >= 1) {
  $value = $mbytes;
  $unit = "Mo";
}

$gbytes = $mbytes / 1024;
if ($gbytes >= 1) {
  $value = $gbytes;
  $unit = "Go";
}
    
// Value with 3 significant digits, thent the unit
$value = round($value, $value > 99 ? 0 : $value >  9 ? 1 : 2);
$totalSizePrint = "$value $unit";

// @todo : forcer l'affichage à chaque étape

mbTrace($chrono, "Chrono :");

echo '<p>Opération terminée (step '.$current.'/'.ceil(72500/$step).').</p>';
echo '<p>'.$total.' ligne lues</p>';
echo '<p>'.$new.' éléments créés ('.$totalSizePrint.'), ';
echo $link.' éléments liés, ';
echo $nofile.' fichiers non trouvés, ';
echo $noconsult.' consultations non trouvés</p><hr>';

if(count($listImport) == $step) {
  echo '<a onclick="javascript:next();">'.(count($listImport)).' suivant >>></a>';
  ?>
  <script type="text/javascript">
    function next() {
      var url = "index.php?m=dPinterop&dialog=1&a=put_courriers";
      url += "&current=<?php echo $current; ?>";
      url += "&new=<?php echo $new; ?>";
      url += "&link=<?php echo $link; ?>";
      url += "&nofile=<?php echo $nofile; ?>";
      url += "&noconsult=<?php echo $noconsult; ?>";
      url += "&totalSize=<?php echo $totalSize; ?>";
      url += "&total=<?php echo $total; ?>";
      window.location.href = url;
    }
    next();
  </script>
  <?php
}

?>