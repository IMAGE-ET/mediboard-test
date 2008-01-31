<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI, $can, $m;

$can->needsRead();

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$object_class    = mbGetValueFromGetOrSession("object_class");
$object_id       = mbGetValueFromGetOrSession("object_id");

// Chargement de la catégorie demandé
$prescription = new CPrescription();
$prescription->load($prescription_id);
$listProduits = array();
if(!$prescription->_id) {
  $prescription->object_class = $object_class;
  $prescription->object_id    = $object_id;
}
if($prescription->object_id) {
  $prescription->loadRefsFwd();
  $prescription->_ref_object->loadRefSejour();
  $prescription->_ref_object->loadRefPatient();
  $prescription->_ref_object->loadRefsPrescriptions();
}
if($prescription->_id) {
  $prescription->loadRefsLines();
  foreach($prescription->_ref_prescription_lines as &$line) {
    $line->_ref_produit->loadRefPosologies();
  }
  // Liste des produits les plus prescrits
  $favoris = CBcbProduit::getFavoris($prescription->praticien_id);
  foreach($favoris as $curr_fav) {
    $produit = new CBcbProduit();
    $produit->load($curr_fav["code_cip"]);
    $listProduits[] = $produit;
  }
}

// Liste des praticiens
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_EDIT);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescription", $prescription);
$smarty->assign("listPrats", $listPrats);
$smarty->assign("listProduits", $listProduits);

$smarty->display("vw_edit_prescription.tpl");

?>