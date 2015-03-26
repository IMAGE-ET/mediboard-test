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
$date_min      = CValue::get('date_min');
$date_max      = CValue::get('date_max');

/** @var CReceiverHL7v2 $receiver */
$receiver = CMbObject::loadFromGuid($receiver_guid);

if (!$receiver && !$receiver->_id && !$receiver->actif) {
  return;
}
$receiver->loadConfigValues();

$where = '';

$echange_hl7v2 = new CExchangeHL7v2();
$ds = $echange_hl7v2->getDS();
$where['sender_id']               = "IS NULL";
$where['receiver_id']             = "= '$receiver->_id'";
$where['message_valide']          = "= '1'";
$where['date_production']         = "BETWEEN '".$date_min."' AND '".$date_max."'";

$exclude_event = "A28|A31|A40";
if ($exclude_event) {
  $exclude_event = explode("|", $exclude_event);
  $where['code'] = $ds->prepareNotIn($exclude_event);
}

/** @var CExchangeHL7v2[] $exchanges */
CSQLDataSource::$trace = true;
$exchanges = $echange_hl7v2->loadList($where, "date_production DESC");
CSQLDataSource::$trace = false;

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
      case "CSejour":
        /** @var CSejour $sejour */
        $sejour = $object;
        $patient = $sejour->loadRefPatient();
        break;

      case "CAffectation":
        /** @var CAffectation $affectation */
        $affectation = $object;
        /** @var CSejour $sejour */
        $sejour  = $affectation->loadRefSejour();
        $patient = $sejour->loadRefPatient();
        break;

      default:
        continue 2;
    }

    $sejour->loadNDA();

    $object->_receiver = $receiver;

    /** @var CHL7v2Event $data_format */
    $data_format = CIHE::getEvent($_exchange);
    $data_format->handle($_exchange->_message);
    $data_format->_exchange_hl7v2 = $_exchange;
    $data_format->_receiver = $receiver;
    /** @var CHL7v2MessageXML $xml */
    $xml = $data_format->message->toXML();

    $PID = $xml->queryNode("PID");
    $nda = $xml->queryTextNode("PID.18", $PID);

    $content = new CContentTabular();
    $content->load($_exchange->message_content_id);

    $content_message = $_exchange->_message;
    if ($nda) {
      continue;
    }

    $temp = explode("\r", $content_message);
    $PID  = $temp[2];

    $temp_2 = explode("|", $PID);

    $nda_found = $temp_2[18];

    $sejour_nda = $sejour->_NDA."^^^WEB100T&&L^AN";

    $temp_2[18] = $sejour_nda;

    $PID = implode("|", $temp_2);

    $temp[2] = $PID;

    $message_inject = implode("\r", $temp);

    $content->content = $message_inject;
    $content->store();

    $evt    = $receiver->getEventMessage($data_format->profil);
    $source = CExchangeSource::get("$receiver->_guid-$evt");

    if (!$source->_id || !$source->active) {
      new CMbException("Source inactive");
    }

    $msg = $content->content;
    if ($receiver->_configs["encoding"] == "UTF-8") {
      $msg = utf8_encode($msg);
    }

    $source->setData($msg, null, $_exchange);
    try {
      $source->send();
    }
    catch (CMbException $e) {
      $_exchange->date_echange = "";
      $_exchange->store();
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