<?php /* $Id: aidesaisie.class.php,v 1.8 2006/02/03 12:28:26 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 1.8 $
* @author Thomas Despoix
*/

require_once( $AppUI->getSystemClass ('mbobject' ) );

require_once( $AppUI->getModuleClass('mediusers') );

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
    $this->CMbObject( 'aide_saisie', 'aide_id' );

    $this->_props["user_id"] = "ref|notNull";
    $this->_props["class"]  = "str|notNull";
    $this->_props["field"]  = "str|notNull";
    $this->_props["name"]   = "str|notNull|confidential";
    $this->_props["text"]   = "text|notNull|confidential";
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
  }
}

?>