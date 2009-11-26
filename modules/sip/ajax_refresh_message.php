<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI;

$echange_hprim_id         = CValue::get("echange_hprim_id");
$echange_hprim_classname  = CValue::get("echange_hprim_classname");

// Chargement de l'objet
$echange_hprim = new $echange_hprim_classname;
$echange_hprim->load($echange_hprim_id);

$domGetIdSourceObject = new CHPrimXMLEvenementsPatients();
$domGetIdSourceObject->loadXML(utf8_decode($echange_hprim->message));
$id400 = new CIdSante400();

switch($echange_hprim->sous_type) {
  case "enregistrementPatient" :
    defineObjectIdClass($domGetIdSourceObject, $id400, $echange_hprim, "CPatient", "hprim:enregistrementPatient", "hprim:patient");
    break;
  case "venuePatient" :
    defineObjectIdClass($domGetIdSourceObject, $id400, $echange_hprim, "CSejour", "hprim:venuePatient", "hprim:venue");
    break;
  case "mouvementPatient" :
    defineObjectIdClass($domGetIdSourceObject, $id400, $echange_hprim, "CSejour", "hprim:mouvementPatient", "hprim:venue");
    break;
  case "fusionVenue" :
    defineObjectIdClass($domGetIdSourceObject, $id400, $echange_hprim, "CSejour", "hprim:fusionVenue", "hprim:venue");
    break;
  default :
    defineObjectIdClass($domGetIdSourceObject, $id400 ,$echange_hprim);
}

$id400->tag = $echange_hprim->emetteur." group:$echange_hprim->group_id";
$id400->id400 = $echange_hprim->_object_id_permanent;
$id400->loadMatchingObject();

if (CAppUI::conf('sip server')) {
  $_echange_hprim->_object_id_permanent = $id400->object_id;
}

$echange_hprim->_object_class = $id400->object_class;
$echange_hprim->_object_id = ($id400->object_id) ? $id400->object_id : $echange_hprim->_object_id_permanent;  

function defineObjectIdClass($domGetIdSourceObject, &$id400, &$echange_hprim, $class = null, $node = null, $type = null) {
  $id400->object_class = $class;
  if ($node && ($echange_hprim->statut_acquittement == "OK")) {
    try {
      $echange_hprim->_object_id_permanent = $domGetIdSourceObject->getIdSourceObject($node, $type);
    } catch(Exception $e) {}
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $echange_hprim);

$smarty->display("inc_echange_hprim.tpl");

?>