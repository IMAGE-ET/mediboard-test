<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
  
if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id  = mbGetValueFromGetOrSession("patient_id", 0);

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsAntecedents();
$patient->loadRefsTraitements();

// Classement des antécédets
$antecedent = new CAntecedent();
$listAnt = array();
foreach($antecedent->_enums["type"] as $antecedent){
  $listAnt["$antecedent"] = array();
}
foreach($patient->_ref_antecedents as $keyAnt=>$currAnt){
  $listAnt["$currAnt->type"][$keyAnt] = $currAnt;
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("listAnt"       , $listAnt);
$smarty->assign("patient", $patient);

$smarty->display("inc_list_ant.tpl");

?>