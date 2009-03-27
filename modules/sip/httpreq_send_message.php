<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
global $AppUI;

$echange_hprim_id         = mbGetValueFromGet("echange_hprim_id");
$echange_hprim_classname  = mbGetValueFromGet("echange_hprim_classname");

// Chargement de l'objet
$echange_hprim = new $echange_hprim_classname;
$echange_hprim->load($echange_hprim_id);

$dest_hprim = new CDestinataireHprim();
$dest_hprim->destinataire = $echange_hprim->destinataire;
$dest_hprim->loadMatchingObject();

mbTrace($echange_hprim);
/*
if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password)) {
  trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
  $AppUI->setMsg("Impossible de joindre le destinataire", UI_MSG_ERROR);
}

// Cas d'une notification
if ($echange_hprim->initiateur_id) {
	$echange_initiateur = new CEchangeHprim();
	$echange_initiateur->load($echange_hprim->initiateur_id);
	
  // Récupère le message d'acquittement après l'execution de la notification d'un evenement patient
  if (null == $acquittement = $client->notificationEvenementPatient($echange_hprim->message, $echange_initiateur->identifiant_emetteur)) {
    trigger_error("Notification d'evenement patient impossible : ".$dest_hprim->url);
    $AppUI->setMsg("Notification d'evenement patient impossible", UI_MSG_ERROR);
  }
} else {
  // Récupère le message d'acquittement après l'execution de l'enregistrement d'un evenement patient
  if (null == $acquittement = $client->evenementPatient($echange_hprim->message)) {
    trigger_error("Evenement patient impossible : ".$dest_hprim->url);
    $AppUI->setMsg("Evenement patient impossible", UI_MSG_ERROR);
  }
}

$echange_hprim->date_echange = mbDateTime();
$echange_hprim->acquittement = $acquittement;
$echange_hprim->store();
*/
$AppUI->setMsg("Message HPRIM envoyé", UI_MSG_OK);

echo $AppUI->getMsg();

?>

