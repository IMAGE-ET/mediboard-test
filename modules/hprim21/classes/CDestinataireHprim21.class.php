<?php

/**
 * Receiver HPRIM 2.1
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Destinataire de messages Hprim21
 * Class CDestinataireHprim21
 */
class CDestinataireHprim21 extends CInteropReceiver {
  // DB Table key
  public $dest_hprim21_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'destinataire_hprim21';
    $spec->key   = 'dest_hprim21_id';
    $spec->messages = array(
      "ADM" => array ("ADM"),
      "REG" => array ("REG"),
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
    $backProps['echanges'] = "CEchangeHprim21 receiver_id";
    
    return $backProps;
  }
  
  function getFormatObjectHandler(CEAIObjectHandler $objectHandler) {
    return null;
  }
  
  function sendEvent($evenement, CMbObject $mbObject) {
    $evenement->_receiver = $this;
    
  }  
}

