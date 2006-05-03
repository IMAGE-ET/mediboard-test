<?php /* $Id: httpreq_vw_admissions.php,v 1.3 2006/04/29 16:49:30 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision: 1.3 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

// Initialisation de variables

$selAdmis = mbGetValueFromGetOrSession("selAdmis", "0");
$selSaisis = mbGetValueFromGetOrSession("selSaisis", "0");
$selTri = mbGetValueFromGetOrSession("selTri", "nom");
$date = mbGetValueFromGetOrSession("date", mbDate());

// Operations de la journée
$today = new COperation;

$ljoin["patients"] = "operations.pat_id = patients.patient_id";
$ljoin["plagesop"] = "operations.plageop_id = plagesop.id";

$where["date_adm"] = "= '$date'";
if($selAdmis != "0") {
  $where["admis"] = "= '$selAdmis'";
  $where["annulee"] = "= 0";
}
if($selSaisis != "0") {
  $where["saisie"] = "= '$selSaisis'";
  $where["annulee"] = "= 0";
}
if($selTri == "nom")
  $order = "patients.nom, patients.prenom, operations.time_adm";
if($selTri == "heure")
  $order = "operations.time_adm, patients.nom, patients.prenom";

$today = $today->loadList($where, $order, null, null, $ljoin);

foreach ($today as $keyOp => $valueOp) {
  $operation =& $today[$keyOp];
  $operation->loadRefsFwd();
  $operation->loadRefsAffectations();
  $affectation =& $operation->_ref_first_affectation;
  if ($affectation->affectation_id) {
    $affectation->loadRefsFwd();
    $affectation->_ref_lit->loadRefsFwd();
    $affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('date', $date);
$smarty->assign('selAdmis', $selAdmis);
$smarty->assign('selSaisis', $selSaisis);
$smarty->assign('selTri', $selTri);
$smarty->assign('today', $today);

$smarty->display('inc_vw_admissions.tpl');

?>