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
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["expediteur_hprimxml"] = "CEchangeHprim sender_id";
    $backProps["expediteur_hprim21"]  = "CEchangeHprim21 sender_id";
    $backProps["expediteur_ihe"]      = "CExchangeIHE sender_id";
    $backProps["expediteur_phast"]    = "CExchangePhast sender_id";
    $backProps["expediteur_any"]      = "CExchangeAny sender_id";
    
    $backProps["config_hprimxml"]     = "CHprimXMLConfig sender_id";
    $backProps["config_hl7"]          = "CHL7Config sender_id";
    
    return $backProps;
  }
  
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "soap", true, $this->_type_echange, false);
  }
  
  function read() {
    $this->loadRefsExchangesSources();
    
    
  }
}

?>