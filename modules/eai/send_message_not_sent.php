<?php 

/**
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$receiver_guid = CValue::get("receiver_guid");
$exclude_event = CValue::get("exclude_event");
$include_event = CValue::get("include_event");

/** @var CReceiverHL7v2 $receiver */
$receiver = CMbObject::loadFromGuid($receiver_guid);

if (!$receiver && !$receiver->_id && !$receiver->actif) {
  return;
}
$receiver->loadConfigValues();

$where = '';

$echange_hl7v2 = new CExchangeHL7v2();
$ds = $echange_hl7v2->getDS();
$where['statut_acquittement']     = "IS NULL";
$where['sender_id']               = "IS NULL";
$where['receiver_id']             = "= '$receiver->_id'";
$where['message_valide']          = "= '1'";
$where['date_echange']            = "IS NULL";
$where['date_production']         = "BETWEEN '".CMbDT::dateTime("-3 DAYS")."' AND '".CMbDT::dateTime("+1 DAYS")."'";
if ($exclude_event) {
  $exclude_event = explode("|", $exclude_event);
  $where['code'] = $ds->prepareNotIn($exclude_event);
}
if ($include_event) {
  $include_event = explode("|", $include_event);
  $where['code'] = $ds->prepareIn($include_event);
}

/** @var CExchangeHL7v2[] $exchanges */
$exchanges = $echange_hl7v2->loadList($where, "date_production DESC");

// Effectue le traitement d'enregistrement des notifications sur lequel le cron vient de passer
// ce qui permet la gestion des doublons
foreach ($exchanges as $_exchange) {
  $_exchange->date_echange = CMbDT::dateTime();
  $_exchange->store();
}

$receiver->synchronous = "1";

foreach ($exchanges as $_exchange) {
  try {
    $_exchange->_ref_receiver = $receiver;
    $object = CMbObject::loadFromGuid("$_exchange->object_class-$_exchange->object_id");
    if (!$object) {
      $_exchange->date_echange = "";
      $_exchange->store();
      continue;
    }

    //Récupération du séjour et du patient en fonction de l'objet
    switch ($_exchange->object_class) {
      case "CPatient":
        /** @var CPatient $patient */
        $patient = $object;
        //Recherche du séjour en cours
        $sejour = reset($patient->getCurrSejour(null, $receiver->group_id));
        //Récupération du dernier séjour
        if (!$sejour) {
          $sejour = reset($patient->loadRefsSejours());
        }
        break;
      case "CSejour":
        /** @var CSejour $sejour */
        $sejour = $object;
        $patient = $sejour->loadRefPatient();
        break;
      default:
        $_exchange->date_echange = "";
        $_exchange->store();
        continue 2;
    }

    $patient->loadIPP();
    if (!$patient->_IPP || $patient->_IPP === "waiting") {
      $_exchange->date_echange = "";
      $_exchange->store();
      continue;
    }

    if ($_exchange->sous_type == "ITI30" && $_exchange->code != "A08") {
      $present_sejour = true;
      $present_patient = $patient && !$patient->_id;
    }
    else {
      $present_patient = $patient && !$patient->_id;
      $present_sejour = $sejour && !$sejour->_id;

      $sejour->loadNDA();
      if (!$sejour->_NDA || $sejour->_NDA === "waiting") {
        $_exchange->date_echange = "";
        $_exchange->store();
        continue;
      }
    }

    //S'il n'y a pas de séjour ou de patient en focntion de la transaction, on passe au prochaine échange
    if ($present_sejour && $present_patient) {
      $_exchange->date_echange = "";
      $_exchange->store();
      continue;
    }

    $object->_receiver = $receiver;

    /** @var CHL7v2Event $data_format */
    $data_format = CIHE::getEvent($_exchange);
    $data_format->handle($_exchange->_message);
    $data_format->_exchange_hl7v2 = $_exchange;
    $data_format->_receiver = $receiver;
    /** @var CHL7v2MessageXML $xml */
    $xml = $data_format->message->toXML();

    $PID = $xml->queryNode("PID");
    $ipp = $xml->queryNode("PID.3", $PID);

    $PV1 = $xml->queryNode("PV1");
    $nda = $xml->queryNode("PV1.19", $PV1);

    if ($_exchange->code != "A40" &&
        (((!$ipp && !$ipp->nodeValue) || $ipp->nodeValue == "0") ||
        (($_exchange->sous_type != "ITI30" ||
        ($_exchange->sous_type == "ITI30" && $_exchange->code == "A08"))  && !$nda && empty($nda->nodeValue)))
    ) {

      CHL7v2Message::setBuildMode($receiver->_configs["build_mode"]);
      $data_format->build($object);
      CHL7v2Message::resetBuildMode();

      $data_format->flatten();
      if (!$data_format->message->isOK(CHL7v2Error::E_ERROR)) {
        $_exchange->date_echange = "";
        $_exchange->store();
        continue;
      }
    }

    $evt    = $receiver->getEventMessage($data_format->profil);
    $source = CExchangeSource::get("$receiver->_guid-$evt");

    if (!$source->_id || !$source->active) {
      new CMbException("Source inactive");
    }

    $msg = $data_format->msg_hl7 ? $data_format->msg_hl7 : $_exchange->_message;
    if ($receiver->_configs["encoding"] == "UTF-8") {
      $msg = utf8_encode($msg);
    }

    $source->setData($msg, null, $_exchange);
    try {
      $source->send();
    }
    catch (CMbException $e) {
      //Si un problème survient lors de l'envoie, on arrête le script pour ne aps rompre la séquentialité
       $e->stepAjax(UI_MSG_ERROR);
    }
  }
  catch (Exception $e) {
    $_exchange->date_echange = "";
    $_exchange->store();
    continue;
  }
}