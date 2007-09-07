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
  static $associations = array(
    "CPatient" => array ("CSpMalade"),
    "CSejour" => array("CSpDossier", "CSpOuvDro", "CSpSejMed"),
  );
    
  function isHandled(CMbObject &$mbObject) {
    return array_key_exists($mbObject->_class_name, self::$associations);
  }
  
  function createSpInstances($mbObject) {
    $spInstances = array();
    foreach(self::$associations[$mbObject->_class_name] as $spClass) {
      $spInstances[] = new $spClass;
    }
    return $spInstances;
  }
  
  function makeId(CMbObject &$mbObject) {
    global $g;
    $id400 = new CIdSante400();
    $id400->tag = "sherpa group:$g";
    $id400->object_class = $mbObject->_class_name;
    $ds =& $id400->_spec->ds;
    
    
    
    switch ($mbObject->_class_name) {
      case "CSejour" : 
	    $year = substr($mbObject->entree_prevue, 3, 1);
	    
	    $min = "00001";
			$max = "29999";
	    
			$idMin = "$year$min";
			$idMax = "$year$max";
			
	    $query = "SELECT MAX(`id400`) ".
	      "\nFROM `id_sante400`".
	      "\nWHERE `tag` = '$id400->tag'".
	      "\nAND `object_class` = '$id400->object_class'".
	      "\nAND `id400` >= '$year$min'".
	      "\nAND `id400` <= '$year$max'";
	      
	    $latestId = $ds->loadResult($query);
	    $newId = $latestId ? $latestId+1 : $idMin;
	    return str_pad($newId, 6, "0", STR_PAD_LEFT);    
      
      case "CPatient" :
      $query = "SELECT MAX(`id400`) ".
        "FROM `id_sante400`".
        "WHERE `tag` = '$id400->tag'".
        "AND `object_class` = '$id400->object_class'";
      $latestId = $ds->loadResult($query);
      $newId = $latestId+1;
      return str_pad($newId, 6, "0", STR_PAD_LEFT);    
        
    }
  }
  
  /**
   * Get all Id400 for given object among all groups
   * @param CMbObject $mbObject
   * @return array of CIdSante400
   */
  function getIds400For(CMbObject &$mbObject) {
    global $g, $m;
    
    // Instance for current group
    $id400 = new CIdSante400;
    $id400->loadLatestFor($mbObject, "sherpa group:$g");
    if (!$id400->_id) {
      $id400->id400 = $this->makeId($mbObject);
      // Update id400;
      $id400->last_update = mbDateTime();
      if ($msg = $id400->store()) {
        trigger_error("Error updating '$mbObject->_view' : $msg", E_USER_WARNING);
        return;
      }
      
      foreach ($this->createSpInstances($mbObject) as $spInstance) {
        // Store sherpa object
	      $spInstance->_id = $id400->id400;
        $spInstance->mapFrom($mbObject);
	      
	      if ($msg = $spInstance->store()) {
	        trigger_error("Error mapping object '$mbObject->_view' : $msg", E_USER_WARNING);
	        return;
	      }
      }
    }
      
    // Get all id400 for mediboard object
    $where["object_class"] = "= '$mbObject->_class_name'";
    $where["object_id"] = "= '$mbObject->_id'";
    $where["tag"] = "LIKE 'sherpa group:%'";
    return $id400->loadList($where);
  }
  
  /**
   * Get Id400 for given object and current group
   * @param CMbObject $mbObject
   * @return CIdSante400
   */
  function getId400For(CMbObject &$mbObject) {
    global $g;
    $id400 = new CIdSante400;
    $id400->loadLatestFor($mbObject, "sherpa group:$g");
    return $id400;
  }

  function onStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
  
    // Propagate modifications to other IDs
    foreach ($this->getIds400For($mbObject) as $id400) {
      // Store id400;
      $id400->last_update = mbDateTime();
      if ($msg = $id400->store()) {
        trigger_error("Error updating id400 for object  '$mbObject->_view' : $msg", E_USER_WARNING);
        continue;
      }
            
      // Find group
      $matches = array();
      if (!preg_match("/sherpa group:([\d]+)/", $id400->tag, $matches)) {
        trigger_error("Found id for propagating has wrong tag '$id400->tag' : $msg", E_USER_WARNING);
        continue;
      }
      $group_id = $matches[1];
      
      // Propagate for all sherpa instances associated to this id400 
      foreach ($this->createSpInstances($mbObject) as $spInstance) {
	      $spInstance->_id = $id400->id400;
        $spInstance->mapFrom($mbObject);
        
	      // Propagated object
	      if ($msg = $spInstance->store()) {
	        trigger_error("Error propagating object '$spInstance->_view' : $msg", E_USER_WARNING);
	        continue;
	      }	      
      }
    }
  }
  
  function onDelete(CMbObject &$mbObject) {}
}

?>