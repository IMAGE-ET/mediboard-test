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

$step = 10;

$current = CValue::get("current", 0);
$new = CValue::get("new", 0);
$link = CValue::get("link", 0);
$nofile = CValue::get("nofile", 0);
$noconsult = CValue::get("noconsult", 0);
$totalSize = CValue::get("totalSize", 0);
$total = CValue::get("total", 0);

$sql = "SELECT * FROM import_fichiers" .
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
    }
    elseif(strpos($file->file_name, ".jpg")) {
      $file->file_type = "image/jpeg";
      $file->file_name = strtoupper($file->file_name);
    }
    else
      $file->file_type = "application/octet-stream";
    $sql = "SELECT *" .
        "\nFROM consultation, plageconsult, import_patients" .
        "\nWHERE consultation.patient_id = import_patients.mb_id" .
        "\nAND import_patients.patient_id = '".$value["pat_id"]."'" .
        "\nAND consultation.plageconsult_id = plageconsult.plageconsult_id" .
        "\nORDER by plageconsult.date DESC, plageconsult.debut DESC";
    $result = $ds->loadlist($sql);
    if(!count($result))
      $noconsult++;
    elseif(!file_exists("modules/dPinterop/doc_recus/".$file->file_name)) {
      $nofile++;
      echo "modules/dPinterop/doc_recus/".$file->file_name."<br>";
    }
    else {
      $consult = $result[0];
      $file->file_size = filesize("modules/dPinterop/doc_recus/".$file->file_name);
      $file->object_class = "CConsultation";
      $file->object_id = $consult["consultation_id"];
      $file->store();
      $file->moveTemp("modules/dPinterop/doc_recus/".$file->file_name);

      $file->store();
      $sql = "UPDATE import_fichiers" .
            "\nSET mb_id = '".$file->file_id."'" .
            "\nWHERE nom = '".$value["nom"]."'";
      $ds->exec($sql);

      $totalSize += $file->file_size;
      $new++;
    }
  } else {
    $sql = "UPDATE import_fichiers" .
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

echo '<p>Opération terminée (step '.$current.'/'.ceil(14000/$step).').</p>';
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
      var url = "index.php?m=dPinterop&dialog=1&a=put_files";
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