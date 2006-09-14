<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage system
 *	@version $Revision$
 *  @author Thomas Despoix
*/

$mp_status = array(
  "all"     => "Tous les messages",
  "past"    => "Déjà publiés",
  "present" => "En cours de publication",
  "future"  => "Publications à venir",
);

/**
 * Classe CMessage. 
 * @abstract Gère les messages de l'administrateur
 */
class CMessage extends CMbObject {
  // DB Table key
	var $message_id = null;	
  
  // DB Fields
  var $deb   = null;
  var $fin   = null;
  var $titre = null;
  var $corps = null;
  
	function CMessage() {
		$this->CMbObject("message", "message_id");
    
    $this->_props["deb"]   = "dateTime|notNull";
    $this->_props["fin"]   = "dateTime|notNull";
    $this->_props["titre"] = "str|maxLength|40|notNull";
    $this->_props["corps"] = "text";
	}

  // Loads messages from a publication date perspective : all, past, present, future
  function loadPublications($status = "all") {
    $now = mbDateTime();
    $where = array();
    
    switch ($status) {
      case "past": 
        $where["fin"] = "< '$now'";
        break;
      case "present": 
        $where["deb"] = "< '$now'";
        $where["fin"] = "> '$now'";
        break;
      case "future": 
        $where["deb"] = "> '$now'";
        break;
    }
    
    return $this->loadList($where);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->titre;
  }
  
}
?>