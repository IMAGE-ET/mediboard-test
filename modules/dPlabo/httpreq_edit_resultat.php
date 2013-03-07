<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */

CCanDo::checkRead();
$user = CUser::get();

$typeListe = CValue::getOrSession("typeListe");

// Chargement de l'item choisi
$prescriptionItem = new CPrescriptionLaboExamen;
$prescriptionItem->load(CValue::getOrSession("prescription_labo_examen_id"));

if ($prescriptionItem->_id) {
  $prescriptionItem->date = CMbDT::date();
}

$siblingItems = array();
if ($prescriptionItem->loadRefs()) {
  
  $siblingItems = $prescriptionItem->loadSiblings();
  $prescriptionItem->_ref_prescription_labo->loadRefs();
  $prescriptionItem->_ref_examen_labo->loadRefsFwd();
  $prescriptionItem->_ref_examen_labo->loadExternal();
  if($prescriptionItem->_ref_prescription_labo->_status >= CPrescriptionLabo::VALIDEE) {
    $prescriptionItem->_locked = 1;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescriptionItem", $prescriptionItem);
$smarty->assign("siblingItems", $siblingItems);
$smarty->assign("user_id", $user->_id);

$smarty->display("inc_edit_resultat.tpl");
?>
