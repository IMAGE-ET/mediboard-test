<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage system
 *	@version $Revision$
 *  @author Thomas Despoix
*/

global $mp_status;

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
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}

  function getSpecs() {
    return array (
      "deb"   => "notNull dateTime",
      "fin"   => "notNull dateTime",
      "titre" => "notNull str maxLength|40",
      "corps" => "text"
    );
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