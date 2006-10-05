<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();

$consultation_id = mbGetValueFromGet("consultation_id");

// Consultation courante
$consult = new CConsultation();
if ($consultation_id) {
  $consult->load($consultation_id);
  $consult->loadRefsDocs();
  $consult->loadRefConsultAnesth();
  $consult->loadRefsFwd();

  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }

  $praticien =& $consult->_ref_chir;
  $patient =& $consult->_ref_patient;
  $patient->loadRefsAntecedents();
  $patient->loadRefsTraitements();
}

// Classement des antécédents
$antecedent = new CAntecedent();
$listAnt = array();
foreach($antecedent->_enumsTrans["type"] as $keyAnt => $currAnt){
  $listAnt[$keyAnt] = array();
}
foreach($patient->_ref_antecedents as $keyAnt => $currAnt){
  $listAnt[$currAnt->type][$keyAnt] = $currAnt;
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("consult", $consult);
$smarty->assign("listAnt", $listAnt);

$smarty->display("print_fiche.tpl");
?>