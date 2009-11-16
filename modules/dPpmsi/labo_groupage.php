<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$sejour_id  = CValue::getOrSession("sejour_id");

if(!$sejour_id) {
  CAppUI::setMsg("Vous devez selectionner un séjour", UI_MSG_ERROR);
  CAppUI::redirect("m=dPpmsi&tab=vw_dossier");
}

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->loadRefGHM();
foreach($sejour->_ref_operations as &$_operation) {
  $_operation->loadRefsFwd();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour" , $sejour              );
$smarty->assign("patient", $sejour->_ref_patient);
$smarty->assign("GHM"    , $sejour->_ref_GHM    );

$smarty->display("labo_groupage.tpl");

?>