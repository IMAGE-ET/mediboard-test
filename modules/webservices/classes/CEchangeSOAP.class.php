<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("system", "CExchangeTransportLayer");

class CEchangeSOAP extends CExchangeTransportLayer {
  // DB Table key
  var $echange_soap_id       = null;
  
  // DB Fields
  var $type                  = null;
  var $web_service_name      = null;
  var $soapfault             = null;
  var $trace                 = null;
  var $last_request_headers  = null;
  var $last_response_headers = null;
  var $last_request          = null;
  var $last_response         = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_soap';
    $spec->key   = 'echange_soap_id';
    return $spec;
  }
  
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
?>