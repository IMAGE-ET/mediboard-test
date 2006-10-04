<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/

class CAideSaisie extends CMbObject {
  // DB Table key
  var $aide_id = null;

  // DB References
  var $user_id = null;

  // DB fields
  var $class = null;
  var $field = null;
  var $name = null;
  var $text = null;
  
  // Referenced objects
  var $_ref_user = null;

  function CAideSaisie() {
    $this->CMbObject("aide_saisie", "aide_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "user_id" => "ref|notNull",
      "class"   => "str|notNull",
      "field"   => "str|notNull",
      "name"    => "str|notNull",
      "text"    => "text|notNull"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_user) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_user->getPerm($permType));
  }
}

?>