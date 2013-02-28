<?php
/**
 * Exchange Transport Layer
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CExchangeTransportLayer extends CMbObject {
  // DB Fields
  public $emetteur;
  public $destinataire;
  public $date_echange;
  public $function_name;
  public $input;
  public $output;
  public $purge;
  public $response_time;
    
  // Form fields
  public $_self_sender;
  public $_self_receiver;
  
  // Filter fields
  public $_date_min;
  public $_date_max;
  
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
