<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Classe CMessage. 
 * @abstract Gère les messages de l'administrateur
 */
class CMessage extends CMbObject {
  // DB Table key
  var $message_id = null; 
  
  // DB fields
  var $module_id = null;
  var $group_id  = null;
  
  var $deb     = null;
  var $fin     = null;
  var $titre   = null;
  var $corps   = null;
  var $urgence = null;
  
  // Form fields
  var $_status = null;
  
  // Object references
  var $_ref_module;
  var $_ref_group;
  
  static $status = array (
    "all"     => "Tous les messages",
    "past"    => "Déjà publiés",
    "present" => "En cours de publication",
    "future"  => "Publications à venir",
  );
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'message';
    $spec->key   = 'message_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["deb"]       = "dateTime notNull";
    $specs["fin"]       = "dateTime notNull";
    $specs["titre"]     = "str notNull maxLength|40";
    $specs["corps"]     = "text notNull";
    $specs["urgence"]   = "enum notNull list|normal|urgent default|normal";
    $specs["module_id"] = "ref class|CModule";
    $specs["group_id"]   = "ref class|CGroups";

    $specs["_status"]   = "enum list|past|present|future";
    return $specs;
  }

  /**
   * Loads messages from a publication date perspective
   * 
   * @param string status   Wanted status, null for all
   * @param string mod_name Module name restriction, null for all
   * @return array          Published messages
   * 
   */ 
  function loadPublications($status = null, $mod_name = null, $group_id = null) {
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
    
    if ($group_id) {
      $where[] = "group_id = '$group_id' OR group_id IS NULL";
    }
    
    $messages = $this->loadList($where, "deb DESC");
    
    // Module name restriction
    if ($mod_name) {
			foreach ($messages as $message_id => $_message) {
			  if ($_message->module_id) {
					if ($_message->loadRefModule()->mod_name != $mod_name) {
						unset($messages[$message_id]);
					}
			  }
			}
    }
    
    return $messages;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->titre;
  }
  
  function loadRefModule() {
    $module = $this->loadFwdRef("module_id", true);
    $this->_view = ($module->_id ? "[$module] - " : "") . $this->titre;
    return $this->_ref_module = $module;
  
  }
  
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }
}

?>