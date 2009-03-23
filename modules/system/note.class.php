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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'note';
    $spec->key   = 'note_id';
    return $spec;
  }

  function getProps() {
  	$specs = parent::getProps();
    $specs["user_id"]      = "ref notNull class|CMediusers";
    $specs["public"]       = "bool notNull";
    $specs["degre"]        = "enum notNull list|low|high default|low";
    $specs["date"]         = "dateTime notNull";
    $specs["libelle"]      = "str notNull";
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
    $key = $object->_spec->key;
    $where["object_class"]     = "= '".get_class($object)."'";
    $where["object_id"] = "= '".$object->$key."'";
    $order = "degre DESC, date DESC";
    $listNote = new CNote();
    $listNote = $listNote->loadListWithPerms($perm, $where, $order);
    return $listNote;
  }
  
}

?>