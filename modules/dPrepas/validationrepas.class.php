<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPrepas
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CValidationRepas class
 */
class CValidationRepas extends CMbObject {
  // DB Table key
  var $validationrepas_id     = null;
    
  // DB Fields
  var $service_id = null;
  var $date           = null;
  var $typerepas_id   = null;
  
  function CValidationRepas() {
    $this->CMbObject("validationrepas", "validationrepas_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "service_id"   => "ref|notNull",
      "date"         => "date",
      "typerepas_id" => "ref|notNull"
    );
  }
}
?>