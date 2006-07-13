<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPplanningOp', 'sejour') );

// Initialisation de variables

$selAdmis = mbGetValueFromGetOrSession("selAdmis", "0");
$selSaisis = mbGetValueFromGetOrSession("selSaisis", "0");
$selTri = mbGetValueFromGetOrSession("selTri", "nom");
$date = mbGetValueFromGetOrSession("date", mbDate());
$next = mbDate("+1 DAY", $date);

// Operations de la journée
$today = new CSejour;

$ljoin["patients"] = "sejour.patient_id = patients.patient_id";

$where["entree_prevue"] = "BETWEEN '$date' AND '$next'";
if($selAdmis != "0") {
  $where[] = "(entree_reelle IS NULL OR entree_reelle = '0000-00-00 00:00:00')";
  $where["annule"] = "= 0";
}
if($selSaisis != "0") {
  $where["saisi_SHS"] = "= '$selSaisis'";
  $where["annule"] = "= 0";
}
if($selTri == "nom")
  $order = "patients.nom, patients.prenom, sejour.entree_prevue";
if($selTri == "heure")
  $order = "sejour.entree_prevue, patients.nom, patients.prenom";

$today = $today->loadList($where, $order, null, null, $ljoin);

foreach ($today as $keySejour => $valueSejour) {
  $sejour =& $today[$keySejour];
  $sejour->loadRefs();
  $affectation =& $sejour->_ref_first_affectation;
  if ($affectation->affectation_id) {
    $affectation->loadRefsFwd();
    $affectation->_ref_lit->loadCompleteView();
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign('date', $date);
$smarty->assign('selAdmis', $selAdmis);
$smarty->assign('selSaisis', $selSaisis);
$smarty->assign('selTri', $selTri);
$smarty->assign('today', $today);

$smarty->display('inc_vw_admissions.tpl');

?>