<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

// Initialisation de variables

$selAdmis  = mbGetValueFromGetOrSession("selAdmis", "0");
$selSaisis = mbGetValueFromGetOrSession("selSaisis", "0");
$selTri    = mbGetValueFromGetOrSession("selTri", "nom");
$date      = mbGetValueFromGetOrSession("date", mbDate());
$next      = mbDate("+1 DAY", $date);

// Operations de la journée
$today = new CSejour;

$ljoin["patients"] = "sejour.patient_id = patients.patient_id";

$where["group_id"] = "= '$g'";
$where["entree_prevue"] = "BETWEEN '$date' AND '$next'";
if($selAdmis != "0") {
  $where[] = "(entree_reelle IS NULL OR entree_reelle = '0000-00-00 00:00:00')";
  $where["annule"] = "= '0'";
}
if($selSaisis != "0") {
  $where["saisi_SHS"] = "= '0'";
  $where["annule"] = "= '0'";
}
if($selTri == "nom")
  $order = "patients.nom, patients.prenom, sejour.entree_prevue";
if($selTri == "heure")
  $order = "sejour.entree_prevue, patients.nom, patients.prenom";

$today = $today->loadList($where, $order, null, null, $ljoin);

foreach ($today as $keySejour => $valueSejour) {
  $sejour =& $today[$keySejour];
//  $sejour->loadRefs();
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $sejour->loadRefsOperations();
  $sejour->loadRefsAffectations();
  $sejour->_ref_patient->verifCmuEtat($date);
  foreach($sejour->_ref_operations as $key_op => $curr_op) {
    $sejour->_ref_operations[$key_op]->loadRefsConsultAnesth();
    //$sejour->_ref_operations[$key_op]->_ref_consult_anesth->loadRefsFwd();
    $sejour->_ref_operations[$key_op]->_ref_consult_anesth->loadRefConsultation();
    $sejour->_ref_operations[$key_op]->_ref_consult_anesth->_ref_consultation->loadRefPlageConsult();
    $sejour->_ref_operations[$key_op]->_ref_consult_anesth->_date_consult =& $sejour->_ref_operations[$key_op]->_ref_consult_anesth->_ref_consultation->_date;
  }
  $affectation =& $sejour->_ref_first_affectation;
  if ($affectation->affectation_id) {
    $affectation->loadRefLit();
    $affectation->_ref_lit->loadCompleteView();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"     , $date);
$smarty->assign("selAdmis" , $selAdmis);
$smarty->assign("selSaisis", $selSaisis);
$smarty->assign("selTri"   , $selTri);
$smarty->assign("today"    , $today);

$smarty->display("inc_vw_admissions.tpl");

?>