<?php 
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     Romain Ollivier <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision:$
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
if ($object instanceof CFactureCabinet) {
  $facture = $object;
  if (CAppUI::conf("ref_pays") == 2) {
    $facture->loadRefsConsults();
    $facture->loadNumerosBVR();
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("reglement", $reglement);
$smarty->assign("object"   , $object);
$smarty->assign("facture"  , $facture);
$smarty->assign("banques"  , $banques);

$smarty->display("edit_reglement.tpl");
?>