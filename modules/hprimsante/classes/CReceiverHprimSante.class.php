<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
/**
 * Destinataire de messages Hprimsante
 * Class CReceiverHprimSante
 */
class CReceiverHprimSante extends CInteropReceiver {
  // DB Table key
  public $receiver_hprimsante_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'receiver_hprimsante';
    $spec->key   = 'receiver_hprimsante_id';
    $spec->messages = array(
      "ADM" => array ("ADM"),
      "REG" => array ("REG"),
      "ORU" => array ("ORU"),
    );

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }

  /**
   * get format of the object handler
   *
   * @param CEAIObjectHandler $objectHandler object handler
   *
   * @return mixed|null
   */
  function getFormatObjectHandler(CEAIObjectHandler $objectHandler) {
    return null;
  }

  /**
   * send event
   *
   * @param CHPrimSanteEvent $evenement evenement
   * @param CMbObject        $mbObject  object
   *
   * @return void
   */
  function sendEvent($evenement, CMbObject $mbObject) {
    $evenement->_receiver = $this;
  }
}