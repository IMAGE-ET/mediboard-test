<?php

/**
 * Exchange Transport Layer
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CExchangeTransportLayer extends CMbObject {
  // DB Fields
  var $emetteur           = null;
  var $destinataire       = null;
  var $date_echange       = null;
  var $function_name      = null;
  var $input              = null;
  var $output             = null;
  var $purge              = null;
  var $response_time      = null;
    
  // Form fields
  var $_self_sender   = null;
  var $_self_receiver = null;
  
  // Filter fields
  var $_date_min          = null;
  var $_date_max          = null;
  
  function getProps() {
    $props = parent::getProps();
    $props["emetteur"]              = "str";
    $props["destinataire"]          = "str";
    $props["date_echange"]          = "dateTime notNull";
    $props["function_name"]         = "str notNull";
    $props["input"]                 = "php show|0";
    $props["output"]                = "php show|0";
    $props["purge"]                 = "bool";
    $props["response_time"]         = "float";

    $props["_self_sender"]          = "bool";
    $props["_self_receiver"]        = "bool";
    $props["_date_min"]             = "dateTime";
    $props["_date_max"]             = "dateTime";
    
    return $props;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_self_sender   = $this->emetteur     == CAppUI::conf('mb_id');
    $this->_self_receiver = $this->destinataire == CAppUI::conf('mb_id');
    
    // ms
    $this->response_time = $this->response_time * 1000;
  }  
}


?>