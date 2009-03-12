<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision: $
 * @author Alexis Granger
 */


$dossier_medical = new CDossierMedical();
$ljoin["patients"] = "patients.patient_id = dossier_medical.object_id";
$where = array();
$where["patients.patient_id"] = " IS NULL";
$where["dossier_medical.object_class"] = " = 'CPatient'";
$dossiers = $dossier_medical->loadList($where, null, null, null, $ljoin);



// Nombre total de dossiers zombies
$nb_zombies = count($dossiers);

foreach($dossiers as &$_dossier){
//mbTrace($_dossier->getDBFields());
  $_dossier->countBackRefs("antecedents");
  $_dossier->countBackRefs("traitements");
  
  if($_dossier->_count["antecedents"] == 0 && $_dossier->_count["traitements"] == 0 && $_dossier->codes_cim == ''){
    unset($dossiers[$_dossier->_id]);
  }
}

$smarty = new CSmartyDP();
$smarty->assign("dossiers", $dossiers);
$smarty->assign("nb_zombies", $nb_zombies);
$smarty->display("check_dossiers_medicaux.tpl");

?>