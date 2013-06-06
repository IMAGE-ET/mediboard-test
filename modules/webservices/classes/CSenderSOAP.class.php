<?php

/**
 * Interop Sender SOAP
 *  
 * @category Webservices
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
  public $sender_soap_id;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'sender_soap';
    $spec->key   = 'sender_soap_id';

    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @see parent::getProps()
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["expediteur_hprimxml"] = "CEchangeHprim sender_id";
    $backProps["expediteur_hprim21"]  = "CEchangeHprim21 sender_id";
    $backProps["expediteur_hl7v2"]    = "CExchangehl7v2 sender_id";
    $backProps["expediteur_dmp"]      = "CExchangeDMP sender_id";
    $backProps["expediteur_phast"]    = "CExchangePhast sender_id";
    $backProps["expediteur_any"]      = "CExchangeAny sender_id";
    
    $backProps["config_hprimxml"]     = "CHprimXMLConfig sender_id";
    $backProps["config_hl7"]          = "CHL7Config sender_id";
    
    return $backProps;
  }

  /**
   * Load exchanges sources
   *
   * @return void
   */
  function loadRefsExchangesSources() {
    $this->_ref_exchanges_sources[] = CExchangeSource::get("$this->_guid", "soap", true, $this->_type_echange, false);
  }
}