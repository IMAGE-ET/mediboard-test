<?php

/**
 * Exchange IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CExchangeIHE 
 * Exchange IHE
 */
CAppUI::requireModuleClass("eai", "CExchangeTabular");

class CExchangeIHE extends CExchangeTabular {
  static $messages = array(
    "PAM" => "CPAM",
  );
  
  // DB Table key
  var $exchange_ihe_id     = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'exchange_ihe';
    $spec->key   = 'exchange_ihe_id';
    
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    
    $props["receiver_id"]   = "ref class|CReceiverIHE"; 
    $props["object_class"]  = "enum list|CPatient|CSejour|COperation|CAffectation show|0";
    
    return $props;
  }
  
  function handle() {
    return COperatorIHE::event($this);
  }

  function getFamily() {
    return self::$messages;
  }
}
?>