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
      "C" => array ( 
        "All",
      ),
      "L" => array ( 
        "All",
      ),
      "R" => array ( 
        "All",
      ),
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
  
  function lastMessage() {
    $echg_hprim21 = new CEchangeHprim21();
    $where = array();
    $where["sender_id"] = " = '$this->_id'";    
    $key = $echg_hprim21->_spec->key;
    $echg_hprim21->loadObject($where, "$key DESC");
    $this->_ref_last_message = $echg_hprim21; 
  }
  
  function getFormatObjectHandler(CEAIObjectHandler $objectHandler) {
    return null;
      
  }
}

?>