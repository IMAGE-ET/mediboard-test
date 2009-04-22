<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI;

$echange_hprim_id         = mbGetValueFromGet("echange_hprim_id");
$echange_hprim_classname  = mbGetValueFromGet("echange_hprim_classname");

// Chargement de l'objet
$echange_hprim = new $echange_hprim_classname;
$echange_hprim->load($echange_hprim_id);

if (CAppUI::conf('sip server')) {
	$domGetIPPPatient = new CHPrimXMLEvenementsPatients();
	$domGetIPPPatient->loadXML(utf8_decode($echange_hprim->message));
	      
	$echange_hprim->_patient_ipp = $domGetIPPPatient->getIPPPatient();
	
	$id400 = new CIdSante400();
	//Paramétrage de l'id 400
	$id400->object_class = "CPatient";
	$id400->tag = CAppUI::conf("mb_id");
	
	$id400->id400 = $echange_hprim->_patient_ipp;
	$id400->loadMatchingObject();
	
	$echange_hprim->_patient_id = $id400->object_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object", $echange_hprim);

$smarty->display("inc_echange_hprim.tpl");

?>