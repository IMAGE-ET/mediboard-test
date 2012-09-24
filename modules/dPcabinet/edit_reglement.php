<?php /* $Id: edit_planning.php 16502 2012-09-03 14:31:26Z flaviencrochard $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 16502 $
* @author Romain Ollivier
*/

CCanDO::checkEdit();

// Chargement du reglement
$reglement = new CReglement();
$reglement->load(CValue::get("reglement_id"));
if ($reglement->_id) {
  $reglement->loadRefsNotes();
  $object = $reglement->loadTargetObject(true);
}
// Préparation du nouveau règlement
else {
  $object = mbGetObjectFromGet("object_class", "object_id", "object_guid");
  $reglement->setObject($object);
  $reglement->date = "now";
  $reglement->emetteur = CValue::get("emetteur");
  $reglement->mode     = CValue::get("mode");
  $reglement->montant  = CValue::get("montant");
}

// Chargement des banques
$banque = new CBanque();
$banques = $banque->loadList(null, "nom");

// Facture de contexte pour l'affichage
if ($object instanceof CFactureConsult) {
  $facture = $object;
}
if ($object instanceof CConsultation) {
  $facture = $object->fakeRefFacture();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("reglement", $reglement);
$smarty->assign("object"   , $object);
$smarty->assign("facture"  , $facture);
$smarty->assign("banques"  , $banques);

$smarty->display("edit_reglement.tpl");
?>