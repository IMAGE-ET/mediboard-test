<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage system
 *	@version $Revision: $
 *  @author Romain Ollivier
*/

/**
 * 
 * Classe CNote. 
 * @abstract Permet de créer des notes sur n'importe quel objet
 */

class CNote extends CMbMetaObject {

  // DB Table key
  var $note_id = null;	
  
  // DB Fields
  var $user_id      = null;
  
  var $public       = null;
  var $degre        = null;
  var $date         = null;
  var $libelle      = null;
  var $text         = null;
  
  // References
  var $_ref_user   = null;
  
   function CNote() {
	  $this->CMbObject("note", "note_id");
      $this->loadRefModule(basename(dirname(__FILE__)));
	}

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["user_id"]      = "notNull ref class|CMediusers";
    $specs["public"]       = "notNull bool";
    $specs["degre"]        = "notNull enum list|low|high default|low";
    $specs["date"]         = "notNull dateTime";
    $specs["libelle"]      = "notNull str";
    $specs["text"]         = "text";
    return $specs;
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_view = "Note écrite par ".$this->_ref_user->_view;
  }
  
  function getPerm($perm) {
    if(!isset($this->_ref_object->_id)) {
      $this->loadRefsFwd();
    }
    return $this->public ?
      $this->_ref_object->getPerm($perm) :
      $this->_ref_object->getPerm($perm) && $this->_ref_user->getPerm($perm);
  }
  
  function loadNotesForObject($object, $perm = PERM_READ) {
    $key = $object->_tbl_key;
    $where["object_class"]     = "= '".get_class($object)."'";
    $where["object_id"] = "= '".$object->$key."'";
    $order = "degre DESC, date DESC";
    $listNote = new CNote();
    $listNote = $listNote->loadListWithPerms($perm, $where, $order);
    return $listNote;
  }
  
}

?>