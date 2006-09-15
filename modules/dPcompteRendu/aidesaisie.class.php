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

    $this->_props["user_id"] = "ref|notNull";
    $this->_props["class"]   = "str|notNull";
    $this->_props["field"]   = "str|notNull";
    $this->_props["name"]    = "str|notNull";
    $this->_props["text"]    = "text|notNull";
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