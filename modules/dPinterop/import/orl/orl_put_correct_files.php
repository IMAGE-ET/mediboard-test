<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();

$chrono = new Chronometer;

set_time_limit( 1800 );
ignore_user_abort( 1 );

$step = 200;

$current = CValue::get("current", 0);
$total = CValue::get("total", 0);
$modif = CValue::get("modif", 0);
$noconsult = CValue::get("noconsult", 0);

$sql = "SELECT * FROM import_fichiers" .
      "\nWHERE mb_id IS NOT NULL" .
      "\nLIMIT ".($current*$step).", $step";
$listImport = $ds->loadlist($sql);


foreach($listImport as $key => $value) {
  
  $chrono->start();
  
  $file = new CFile;
  $file->load($value["mb_id"]);
  $consult = new CConsultation;
  $consult->load($file->file_consultation);
  $consult->loadRefsFwd();
  $consult->_ref_plageconsult->loadRefsFwd();
  if($consult->_ref_plageconsult->_ref_chir->function_id != 11) {
    //mbTrace($consult, "consult");
    $sql = "SELECT *" .
        "\nFROM consultation, plageconsult, users_mediboard" .
        "\nWHERE consultation.patient_id = '".$consult->patient_id."'" .
        "\nAND consultation.plageconsult_id = plageconsult.plageconsult_id" .
        "\nAND plageconsult.chir_id = users_mediboard.user_id" .
        "\nAND users_mediboard.function_id = '11'" .
        "\nORDER by plageconsult.date DESC, plageconsult.debut DESC";
    $result = $ds->loadlist($sql);
    if(!count($result)) {
      $noconsult++;
    } else {
			$file->object_class = "CConsultation";
			$file->object_id = $result[0]["consultation_id"];
      $file->store();
			$file->moveTemp("files/consultations/".$file->file_consultation);
      $modif++;
    }
  }
  $total++;
  $chrono->stop();
}
$current++;

// @todo : forcer l'affichage à chaque étape

mbTrace($chrono, "Chrono :");

echo '<p>Opération terminée.</p>';
echo '<p>'.$total.' ligne lues</p>';
echo '<p>'.$modif.' éléments modifiés, '.$noconsult.' consultations non trouvée</p><hr>';

if(count($listImport) == $step) {
  echo '<a onclick="javascript:next();">'.(count($listImport)).' suivant >>></a>';
  ?>
  <script type="text/javascript">
    function next() {
      var url = "index.php?m=dPinterop&dialog=1&a=put_correct_files";
      url += "&current=<?php echo $current; ?>";
      url += "&total=<?php echo $total; ?>";
      url += "&modif=<?php echo $modif; ?>";
      url += "&noconsult=<?php echo $noconsult; ?>";
      window.location.href = url;
    }
    next();
  </script>
  <?php
}

?>