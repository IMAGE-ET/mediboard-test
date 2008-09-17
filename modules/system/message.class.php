<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage system
 *  @version $Revision$
 *  @author Thomas Despoix
*/

/**
 * Classe CMessage. 
 * @abstract Gère les messages de l'administrateur
 */
class CMessage extends CMbObject {
  // DB Table key
  var $message_id = null; 
  
  // DB Fields
  var $deb     = null;
  var $fin     = null;
  var $titre   = null;
  var $corps   = null;
  var $urgence = null;
  var $module_id = null;
  
  var $_ref_module;
  
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

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["deb"]       = "notNull dateTime";
    $specs["fin"]       = "notNull dateTime";
    $specs["titre"]     = "notNull str maxLength|40";
    $specs["module_id"] = "ref class|CModule";
    $specs["corps"]     = "text";
    $specs["urgence"]   = "notNull enum list|normal|urgent default|normal";
    return $specs;
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
    $this->loadRefsFwd();
    $this->_view = (($this->module_id)?'['.$this->_ref_module->_view.'] - ':''). $this->titre;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    if ($this->module_id) {
      $this->_ref_module = new CModule();
        $this->_ref_module->load($this->module_id);
    }
  }
}

?>