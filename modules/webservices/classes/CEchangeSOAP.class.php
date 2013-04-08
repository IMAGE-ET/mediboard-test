<?php

/**
 * Exchange SOAP
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CEchangeSOAP
 * Exchange SOAP
 */
class CEchangeSOAP extends CExchangeTransportLayer {
  // DB Table key
  public $echange_soap_id;
  
  // DB Fields
  public $type;
  public $web_service_name;
  public $soapfault;
  public $trace;
  public $last_request_headers;
  public $last_response_headers;
  public $last_request;
  public $last_response;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_soap';
    $spec->key   = 'echange_soap_id';
    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @see parent::getProps()
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["type"]                  = "str";
    $props["web_service_name"]      = "str";
    $props["soapfault"]             = "bool";
    $props["trace"]                 = "bool";
    $props["last_request_headers"]  = "text";
    $props["last_response_headers"] = "text";
    $props["last_request"]          = "xml";
    $props["last_response"]         = "xml";
    
    return $props;
  }
}