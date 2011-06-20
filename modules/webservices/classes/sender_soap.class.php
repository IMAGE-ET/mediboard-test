<?php

/**
 * Interop Sender SOAP
 *  
 * @category FTP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSenderSOAP 
 * Interoperability Sender SOAP
 */
class CSenderSOAP extends CInteropSender {
  // DB Table key
  var $sender_soap_id  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sender_soap';
    $spec->key   = 'sender_soap_id';

    return $spec;
  }
  
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "soap", true, $this->_type_echange);
  }
  
  function read() {
    $this->loadRefsExchangesSources();
    
    
  }
}

?>