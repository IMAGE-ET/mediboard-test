<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

require_once($AppUI->getSystemClass("mbobject"));


class CTechniqueComp extends CMbObject {
  // DB Table key
  var $technique_id = null;

  // DB References
  var $consultAnesth_id = null;

  // DB fields
  var $technique  = null;

  function CTechniqueComp() {
    $this->CMbObject("techniques_anesth", "technique_id");

    $this->_props["technique_id"]     = "ref|notNull";
    $this->_props["consultAnesth_id"] = "ref|notNull";
    $this->_props["technique"]        = "str";
  }
}

?>