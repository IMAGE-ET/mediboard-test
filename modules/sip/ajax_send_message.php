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

$where = '';
if (!$echange_hprim_id) {
  if (!($limit = CAppUI::conf('sip batch_count'))) {
    return;
  }
	$echange_hprim = new CEchangeHprim();
  $where['statut_acquittement'] = "IS NULL";
	$where['emetteur'] = " = '".CAppUI::conf("mb_id")."'";
	$where['message_valide'] = " = '1'";
  $where['acquittement_valide'] = "IS NULL"; 
	
  $notifications = $echange_hprim->loadList($where, null, $limit);
  // Effectue le traitement d'enregistrement des notifications sur lequel le cron vient de passer
  // ce qui permet la gestion des doublons
  foreach ($notifications as $notification) {
  	$notification->date_echange = mbDateTime();
    $notification->store();
  }
  
  foreach ($notifications as $notification) {  
    $notification->uncompressFields();  
    
    $dest_hprim = new CDestinataireHprim();
	  $dest_hprim->nom = $notification->destinataire;
	  
	  $dest_hprim->loadMatchingObject();

	  if (!($client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password, "hprimxml"))) {
	    trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
	  }
	  
	  // Récupère le message d'acquittement après l'execution de l'enregistrement d'un evenement patient
	  if (!($acquittement = $client->evenementPatient($notification->_uncompressed["message"]))) {
	    trigger_error("Evenement patient impossible : ".$dest_hprim->url);
	  }
	  
	  $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
	  $domGetAcquittement->loadXML(utf8_decode($acquittement));
    $doc_valid = $domGetAcquittement->schemaValidate();
    if ($doc_valid) {
    	$notification->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
    }
    $notification->acquittement_valide = $doc_valid ? 1 : 0;
    
	  $notification->date_echange = mbDateTime();
	  $notification->acquittement = $acquittement;
	  $notification->store();
  }
} else {
	// Chargement de l'objet
	$echange_hprim = new $echange_hprim_classname;
	$echange_hprim->load($echange_hprim_id);
	$echange_hprim->uncompressFields();  
  
	$dest_hprim = new CDestinataireHprim();
	$dest_hprim->nom = $echange_hprim->destinataire;
	$dest_hprim->loadMatchingObject();

	if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password, "hprimxml")) {
	  trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
	  CAppUI::setMsg("Impossible de joindre le destinataire", UI_MSG_ERROR);
	}
	
	// Récupère le message d'acquittement après l'execution de l'enregistrement d'un evenement patient
  mbTrace($echange_hprim->_uncompressed["message"], "message", true);
	if (null == $acquittement = $client->evenementPatient($echange_hprim->_uncompressed["message"])) {
	  trigger_error("Evenement patient impossible : ".$dest_hprim->url);
	  CAppUI::setMsg("Evenement patient impossible", UI_MSG_ERROR);
	}

  $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
  $domGetAcquittement->loadXML(utf8_decode($acquittement));
  $doc_valid = $domGetAcquittement->schemaValidate();
  if ($doc_valid) {
    $echange_hprim->statut_acquittement = $domGetAcquittement->getStatutAcquittementPatient();
  }
  $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
    
	$echange_hprim->date_echange = mbDateTime();
	$echange_hprim->acquittement = $acquittement;

	$echange_hprim->store();
	
	CAppUI::setMsg("Message HPRIM envoyé", UI_MSG_OK);
	
	echo CAppUI::getMsg();
}

?>