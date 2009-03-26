<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
global $AppUI;

$message_hprim_id         = mbGetValueFromGet("message_hprim_id");
$message_hprim_classname  = mbGetValueFromGet("message_hprim_classname");

// Chargement de l'objet
$msg_hprim = new $message_hprim_classname;
$msg_hprim->load($message_hprim_id);

$dest_hprim = new CDestinataireHprim();
$dest_hprim->destinataire = $msg_hprim->destinataire;
$dest_hprim->loadMatchingObject();

if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password)) {
  trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
  $AppUI->setMsg("Impossible de joindre le destinataire", UI_MSG_ERROR);
}

// Cas d'une notification
if ($message_hprim->initiateur_id) {
  // Récupère le message d'acquittement après l'execution de la notification d'un evenement patient
  if (null == $acquittement = $client->notificationEvenementPatient($msg_hprim->message, $msg_hprim->initiateur_id)) {
    trigger_error("Notification d'evenement patient impossible : ".$dest_hprim->url);
    $AppUI->setMsg("Notification d'evenement patient impossible", UI_MSG_ERROR);
  }
} else {
  // Récupère le message d'acquittement après l'execution de l'enregistrement d'un evenement patient
  if (null == $acquittement = $client->evenementPatient($msg_hprim->message)) {
    trigger_error("Evenement patient impossible : ".$dest_hprim->url);
    $AppUI->setMsg("Evenement patient impossible", UI_MSG_ERROR);
  }
}

$msg_hprim->date_echange = mbDateTime();
$msg_hprim->acquittement = $acquittement;
$msg_hprim->store();

$AppUI->setMsg("Message HPRIM envoyé", UI_MSG_OK);

echo $AppUI->getMsg();

?>

