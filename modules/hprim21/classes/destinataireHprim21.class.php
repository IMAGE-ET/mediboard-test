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

CAppUI::requireModuleClass("eai", "interop_receiver");

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
    
    $props["message"]     = "enum list|L|C|R default|C";

    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['emetteurs']      = "CEchangeHprim21 emetteur_id";
    $backProps['destinataires']  = "CEchangeHprim21 destinataire_id";
    
    return $backProps;
  }
    
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources = array();
    foreach ($this->_spec->messages as $_message => $_evenements) {
      if ($_message == $this->message) {
        foreach ($_evenements as $_evenement) {
          $this->_ref_exchanges_sources[$_evenement] = CExchangeSource::get("$this->_guid-$_evenement", null, true, $this->_type_echange);
        }
      }
    }
  }
}

?>