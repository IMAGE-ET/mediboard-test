<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage system
 *	@version $Revision: $
 *  @author Romain Ollivier
*/

/**
 * Classe CNote. 
 * @abstract Permet de créer des notes sur n'importe quel objet
 */
class CNote extends CMbObject {

  // DB Table key
	var $note_id = null;	
  
  // DB Fields
  var $user_id      = null;
  var $object_id    = null;
  var $object_class = null;
  var $public       = null;
  var $degre        = null;
  var $date         = null;
  var $libelle      = null;
  var $text         = null;
  
  // References
  var $_ref_user   = null;
  var $_ref_object = null;
  
	function CNote() {
		$this->CMbObject("note", "note_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}

  function getSpecs() {
    return array (
      "user_id"      => "notNull ref class|CMediusers",
      "object_id"    => "notNull ref",
      "object_class" => "notNull str maxLength|25",
      "public"       => "notNull bool",
      "degre"        => "notNull enum list|low|high default|low",
      "date"         => "notNull dateTime",
      "libelle"      => "notNull str",
      "text"         => "text"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id);
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