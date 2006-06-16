<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $tab, $dPconfig;
require_once( $AppUI->getModuleClass('dPplanningOp', 'sejour') );

$sejour_id  = mbGetValueFromGet("sejour_id", 0);

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefsFwd();
$praticien =& $sejour->_ref_praticien;
$patient =& $sejour->_ref_patient;
$patient->loadRefsSejours();
$sejours =& $patient->_ref_sejours;

// L'utilisateur est-il un praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

// Vérification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);

$sejourConfig =& $dPconfig["dPplanningOp"]["sejour"];
for ($i = $sejourConfig["heure_deb"]; $i <= $sejourConfig["heure_fin"]; $i++) {
    $hours[] = $i;
}

for ($i = 0; $i < 60; $i += $sejourConfig["min_intervalle"]) {
    $mins[] = $i;
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign("sejour"   , $sejour);
$smarty->assign("praticien", $praticien);
$smarty->assign("patient"  , $patient);
$smarty->assign("sejours"  , $sejours);

$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("hours"      , $hours);
$smarty->assign("mins"       , $mins);

$smarty->display('inc_form_sejour.tpl');

?>