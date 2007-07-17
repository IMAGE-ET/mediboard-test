<?php /* $Id: mbobject.class.php 2252 2007-07-12 10:00:15Z rhum1 $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: 1793 $
 *  @author Thomas Despoix
*/

/**
 * Class CMbObjectHandler 
 * @abstract Event handler class for CMbObject
 */

class CSpObjectHandler extends CMbObjectHandler {
  
  function getSpInstance(CMbObject &$mbObject) {
    switch ($mbObject->_class_name) {
      case "CPatient": return new CSpMalade;
      default : return null;
    }
  }
  
  function getIdsFor(CMbObject &$mbObject) {
    global $g, $m;
    
    // Instance for current group
    $id400 = new CIdSante400;
    $id400->loadLatestFor($mbObject, "sherpa group:$g");
    if (!$id400->_id) {
      // Create sherpa instance
      $spInstance = $this->getSpInstance($mbObject);
      $spInstance->mapFrom($mbObject);
      if ($msg = $spInstance->store()) {
        trigger_error("Error mapping object '$mbObject->_view' : $msg", E_USER_WARNING);
        return;
      }

      // Store id400;
      $id400->id400 = $spInstance->_id;
      $id400->last_update = mbDateTime();
      if ($msg = $id400->store()) {
        trigger_error("Error creating id400 for object  '$mbObject->_view' : $msg", E_USER_WARNING);
        return;
      }
    }
      
    // Get all id400 for mediboard object
    $where["object_class"] = "= '$mbObject->_class_name'";
    $where["object_id"] = "= '$mbObject->_id'";
    $where["tag"] = "LIKE 'sherpa group:%'";
    return $id400->loadList($where);
  }
  
  function onStore(CMbObject &$mbObject) {
    if (!$this->getSpInstance($mbObject)) {
      return;
    }
  
    // Propagate modifications to other IDs
    foreach ($this->getIdsFor($mbObject) as $id400) {
      $spInstance = $this->getSpInstance($mbObject);
      $spInstance->mapFrom($mbObject);
      $spInstance->_id = $id400->id400;
      
      // Find group
      $matches = array();
      if (!preg_match("/sherpa group:([\d]+)/", $id400->tag, $matches)) {
        trigger_error("Found id for propagating has wrong tag '$id400->tag' : $msg", E_USER_WARNING);
        continue;
      }
      
      $group_id = $matches[1];

      // CHANGE DATA SOURCE NAME !!!
     
      // Propagated object
      if ($msg = $spInstance->store()) {
        trigger_error("Error propagating object '$spInstance->_view' : $msg", E_USER_WARNING);
        continue;
      }
      
      // Store id400;
      $id400->id400 = $spInstance->_id;
      $id400->last_update = mbDateTime();
      if ($msg = $id400->store()) {
        trigger_error("Error updating id400 for object  '$mbObject->_view' : $msg", E_USER_WARNING);
        continue;
      }
    }    
  }
  
  function onDelete(CMbObject &$mbObject) {}
}

?>