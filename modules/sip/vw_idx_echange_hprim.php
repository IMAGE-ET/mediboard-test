<?php /* $Id$ */
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$echange_hprim_id = mbGetValueFromGet("echange_hprim_id");

$observations = array();

// Chargement de l'échange HPRIM demandé
$echange_hprim = new CEchangeHprim();
$echange_hprim->load($echange_hprim_id);
if($echange_hprim->load($echange_hprim_id)) {
	$echange_hprim->loadRefs();	
	
	if ($echange_hprim->acquittement) {
		$domGetAcquittement = new CHPrimXMLAcquittementsPatients();
    $domGetAcquittement->loadXML(utf8_decode($echange_hprim->acquittement));
  
    $observations = $domGetAcquittement->getAcquittementObservation();
	}
}

// Récupération de la liste des echanges HPRIM
$itemEchangeHprim = new CEchangeHprim;
$where["initiateur_id"] = "IS NULL";
$listEchangeHprim = $itemEchangeHprim->loadList($where);
foreach($listEchangeHprim as &$curr_echange_hprim) {
	$curr_echange_hprim->loadRefNotifications();
	if (!empty($curr_echange_hprim->_ref_notifications)) {
		foreach ($curr_echange_hprim->_ref_notifications as $_curr_ref_notification) {
	    $domGetIPPPatient = new CHPrimXMLEvenementsPatients();
	    $domGetIPPPatient->loadXML(utf8_decode($_curr_ref_notification->message));
	
	    $_curr_ref_notification->_patient_ipp = $domGetIPPPatient->getIPPPatient();
	   
	    $id400 = new CIdSante400();
	    //Paramétrage de l'id 400
	    $id400->object_class = "CPatient";
	    $id400->tag = $_curr_ref_notification->emetteur;
	  
	    $id400->id400 = $_curr_ref_notification->_patient_ipp;
	    $id400->loadMatchingObject();
	    
	    $_curr_ref_notification->_patient_id = ($id400->object_id) ? $id400->object_id : $_curr_ref_notification->_patient_ipp;
		}
	} 
	$domGetIPPPatient = new CHPrimXMLEvenementsPatients();
	$domGetIPPPatient->loadXML(utf8_decode($curr_echange_hprim->message));

	$curr_echange_hprim->_patient_ipp = $domGetIPPPatient->getIPPPatient();
	
	$id400 = new CIdSante400();
	//Paramétrage de l'id 400
	$id400->object_class = "CPatient";
	$id400->tag = $curr_echange_hprim->emetteur;

	$id400->id400 = $curr_echange_hprim->_patient_ipp;
	$id400->loadMatchingObject();
  
	if (CAppUI::conf('sip server')) {
		$curr_echange_hprim->_patient_ipp = $id400->object_id;
	}
	$curr_echange_hprim->_patient_id = ($id400->object_id) ? $id400->object_id : $curr_echange_hprim->_patient_ipp;	
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("echange_hprim"    , $echange_hprim);
$smarty->assign("observations"     , $observations);
$smarty->assign("listEchangeHprim" , $listEchangeHprim);
$smarty->display("vw_idx_echange_hprim.tpl");
?>
