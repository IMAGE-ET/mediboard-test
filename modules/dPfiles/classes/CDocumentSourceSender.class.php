<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * DMP document sender
 */
class CDocumentSourceSender extends CDocumentSender {
  /**
   * @see parent::send()
   */
  function send(CCompteRendu $docItem) {
    $object = $docItem->loadTargetObject();
    if ($object instanceof CConsultAnesth) {
      $object = $object->loadRefConsultation();
    }

    if ($object instanceof CPatient) {
      CAppUI::stepAjax("Impossible d'ajouter un document lié directement à un patient", UI_MSG_ERROR);
    }
    $receiver_hl7v3           = new CReceiverHL7v3();
    $receiver_hl7v3->actif    = 1;
    $receiver_hl7v3->group_id = CGroups::loadCurrent()->_id;

    /** @var CReceiverHL7v3[] $receivers */
    $receivers = $receiver_hl7v3->loadMatchingList();

    foreach ($receivers as $_receiver) {
      $request = $_receiver->sendEventProvideAndRegisterDocumentSetRequest($docItem);
      mbTrace($request);
    }
  }

  /**
   * @see parent::cancel()
   */
  function cancel(CCompteRendu $docItem) {

  }

  /**
   * @see parent::resend()
   */
  function resend(CCompteRendu $docItem) {

  }

  /**
   * @see parent::getSendProblem()
   */
  function getSendProblem(CCompteRendu $docItem) {

  }
}
