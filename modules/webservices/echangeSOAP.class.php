<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CEchangeSOAP extends CMbObject {
  // DB Table key
  var $echange_soap_id      = null;
  
  // DB Fields
  var $emetteur             = null;
  var $destinataire         = null;
  var $type                 = null;
  var $date_echange         = null;
  var $web_service_name     = null;
  var $function_name        = null;
  var $input                = null;
  var $output               = null;
  var $soapfault            = null;
    
  // Form fields
  var $_self_emetteur       = null;
  var $_self_destinataire   = null;
  
  // Filter fields
  var $_date_min            = null;
  var $_date_max            = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'echange_soap';
    $spec->key   = 'echange_soap_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["emetteur"]              = "str";
    $specs["destinataire"]          = "str";
    $specs["type"]                  = "str";
    $specs["date_echange"]          = "dateTime notNull";
    $specs["web_service_name"]      = "str";
    $specs["function_name"]         = "str notNull";
    $specs["input"]                 = "php notNull";
    $specs["output"]                = "php";
    $specs["soapfault"]             = "bool";
    $specs["_self_emetteur"]        = "bool";
    $specs["_self_destinataire"]    = "bool";
    $specs["_date_min"]             = "dateTime";
    $specs["_date_max"]             = "dateTime";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_self_emetteur = $this->emetteur == CAppUI::conf('mb_id');
    $this->_self_destinataire = $this->destinataire == CAppUI::conf('mb_id');
  }
}
?>