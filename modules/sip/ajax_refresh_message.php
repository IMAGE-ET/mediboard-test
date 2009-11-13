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

if (CAppUI::conf('sip server')) {
	$domGetIdSourceObject = new CHPrimXMLEvenementsPatients();
	$domGetIdSourceObject->loadXML(utf8_decode($echange_hprim->message));
	
  $id400 = new CIdSante400();
	if ($_echange_hprim->sous_type == "enregistrementPatient" ) {
    $id400->object_class = "CPatient";
    $_echange_hprim->_object_id_permanent = $domGetIdSourceObject->getIdSourceObject("hprim:enregistrementPatient", "hprim:patient");
  }
  if ($_echange_hprim->sous_type == "venuePatient" ) {
    $id400->object_class = "CSejour";
    $_echange_hprim->_object_id_permanent = $domGetIdSourceObject->getIdSourceObject("hprim:venuePatient", "hprim:venue");
  }
	
  $id400->tag = $echange_hprim->emetteur;
  $id400->id400 = $echange_hprim->_object_id_permanent;
  $id400->loadMatchingObject();
  
  if (CAppUI::conf('sip server')) {
    $echange_hprim->_object_id_permanent = $id400->object_id;
  }
  $echange_hprim->_object_class = $id400->object_class;
  $echange_hprim->_object_id = ($id400->object_id) ? $id400->object_id : $echange_hprim->_object_id_permanent;  
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $echange_hprim);

$smarty->display("inc_echange_hprim.tpl");

?>