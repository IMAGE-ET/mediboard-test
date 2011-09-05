<?php

/**
 * Receiver IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CReceiverIHE 
 * Receiver IHE
 */
CAppUI::requireModuleClass("eai", "CInteropReceiver");

class CReceiverIHE extends CInteropReceiver {
  // DB Table key
  var $receiver_ihe_id  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'receiver_ihe';
    $spec->key   = 'receiver_ihe_id';
    $spec->messages = array(
      "PAM" => array ( 
        "evenementsPatient" 
      ),
    );
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['echanges'] = "CExchangeIHE receiver_id";
    
    return $backProps;
  }
  
  function loadRefsExchangesSources() {
    if (!$this->_ref_msg_supported_family) {
      $this->getMessagesSupportedByFamily();
    }

    $this->_ref_exchanges_sources = array();
    foreach ($this->_ref_msg_supported_family as $_evenement) {
      $this->_ref_exchanges_sources[$_evenement] = CExchangeSource::get("$this->_guid-$_evenement", null, true, $this->_type_echange);
    }
  }
}
?>