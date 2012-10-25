<?php

/**
 * Receiver HPRIM 2.1
 *  
 * @category HPRIM 2.1
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CDestinataireHprim21 extends CInteropReceiver {
  // DB Table key
  var $dest_hprim21_id  = null;
      
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
  
  function getProps() {
    $props = parent::getProps();
   
    return $props;
  }
  
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

?>