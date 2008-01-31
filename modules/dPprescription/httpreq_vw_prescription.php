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
//$object_class = "CSejour";
//$object_id = 35976;

// Chargement de la catégorie demandé
$prescription = new CPrescription();
$prescription->load($prescription_id);
$listProduits = array();
if(!$prescription->_id) {
  $prescription->object_class = $object_class;
  $prescription->object_id    = $object_id;
} else {
  // Liste des médicaments les plus prescrits
  $favoris = CBcbProduit::getFavoris($prescription->praticien_id);
  foreach($favoris as $curr_fav) {
    $produit = new CBcbProduit();
    $produit->load($curr_fav["code_cip"]);
    $listProduits[] = $produit;
  }
}
if($prescription->object_id) {
  $prescription->loadRefsFwd();
  $prescription->loadRefsLines();
  foreach($prescription->_ref_prescription_lines as &$line) {
    $line->_ref_produit->loadRefPosologies();
  }
  $prescription->_ref_object->loadRefSejour();
  $prescription->_ref_object->loadRefPatient();
}

// Liste des praticiens
$user = new CMediusers();
$listPrats = $user->loadPraticiens(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescription", $prescription);
$smarty->assign("listPrats", $listPrats);
$smarty->assign("listProduits", $listProduits);

$smarty->display("inc_vw_prescription.tpl");

?>