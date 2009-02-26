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
  static $handled = array (
    "CPatient" => array ("CSpMalade"),
    "CSejour" => array ("CSpSejMed", "CSpDossier", "CSpOuvDro", "CSpUrgDos", "CSpUrgDro"),
  );
  
  static $queriable = array (
    "CPatient", 
    "CSejour", 
    "COperation",
    "CMediusers",
	);
  
  
  static function isHandled(CMbObject &$mbObject) {
    return array_key_exists($mbObject->_class_name, self::$handled);
  }
  
  static function createSpInstances($mbObject) {
    $spInstances = array();
    foreach(self::$handled[$mbObject->_class_name] as $spClass) {
      $spInstances[] = new $spClass;
    }
    return $spInstances;
  }
  
  /**
   * Check id for object
   */
  static function checkId(CMbObject &$mbObject, $id) {
    if ($mbObject instanceof CSejour) {
      // Vérification de l'année
      $yearWanted = substr($mbObject->entree_prevue, 3, 1);
	    $yearGiven = substr($id, 0, 1);
	    if ($yearGiven != $yearWanted) {
	      return false;
	    }
	    
	    $counter = substr($id, 1, 1);
	    if ($mbObject->type == "urg" && !$mbObject->zt && !in_range($counter, "5", "9")) {
	      return false;
	    }
	    
	    if (($mbObject->type != "urg" || $mbObject->zt) && !in_range($counter, "0", "2")) {
	      return false;
	    }
    }

    return true;    
  }
      
  /**
   * Generate a Sherpa DSN unique identifier for a given Mediboard object
   * @param $mbObject CMbObject The Mediboard object
   * @return numchar The generated identifier
   */
  static function makeId(CMbObject &$mbObject) {
    global $g;
    $id400 = new CIdSante400();
    $id400->tag = "sherpa group:$g";
    $id400->object_class = $mbObject->_class_name;
    $ds =& $id400->_spec->ds;
    
    // Sejour
    if ($mbObject instanceof CSejour) {
	    $year = substr($mbObject->entree_prevue, 3, 1);
	    
      // Hospitalisation standard
	    $min = "00001";
			$max = "29999";
			
			if ($mbObject->type == "urg" && !$mbObject->zt) {
				$min = "50001";
			  $max = "99999";
			}
	    
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
    }
    
    // Patient
    if ($mbObject instanceof CPatient) {
      if ($mbObject->_merging) {
        return;
      }
      
      $query = "SELECT MAX(`id400`) ".
        "FROM `id_sante400`".
        "WHERE `tag` = '$id400->tag'".
        "AND `object_class` = '$id400->object_class'";
      $latestId = $ds->loadResult($query);
      $newId = $latestId+1;
      return str_pad($newId, 6, "0", STR_PAD_LEFT);
    }
    
    // Operation
    if ($mbObject instanceof COperation) {
    	$spInstance = new CSpEntCCAM();
    	$spInstance->getCurrentDataSource();
    	$spInstance->makeId($mbObject);
    	return $spInstance->_id;
    }
  }
  
  /**
   * Get all Id400 for given object among all groups
   * Will create one for current group if necessary
   * @param CMbObject $mbObject
   * @return array|CIdSante400
   */
  static function getIds400For(CMbObject &$mbObject) {
    global $g;
    
    // Instance for current group
    $id400 = self::getId400For($mbObject);
    if (!$id400->_id) {
      // May not create if id should not be built (e.g. when merging)
      if ($id400->id400 = self::makeId($mbObject)) {
        $id400->last_update = mbDateTime();
	      if ($msg = $id400->store()) {
	        trigger_error("Error updating '$mbObject->_class_name' '$mbObject->_id' : $msg", E_USER_WARNING);
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
  static function getId400For(CMbObject &$mbObject) {
    global $g;

//  Should not use CIdSante400::loadLatestFor(); 
//    $id400 = new CIdSante400;
//    $id400->loadLatestFor($mbObject, "sherpa group:$g");

    $order = "id400 ASC";
    $id400 = new CIdSante400;
    $id400->tag = "sherpa group:$g";
    $id400->object_class = $mbObject->_class_name;
    $id400->object_id    = $mbObject->_id;
    
    if (!$mbObject->_id) {
      return $id400;
    }
    
    $id400->loadMatchingObject($order);

    // Necessary to empty values
    if ($id400->id400 === null) {
      $id400->id400 = "";
    }
    
    return $id400;
  }

  /**
   * Get MbObject for a Sherpa Id400
   * @param mbClass $mbObject
   * @return int $id
   */
  static function getMbObjectFor($mbClass, $id) {
    if (!$id) {
      return new $mbClass;
    }
    
    global $g;
    $order = "id400 ASC";
    $id400 = new CIdSante400;
    $id400->tag = "sherpa group:$g";
    $id400->object_class = $mbClass;
    $id400->id400 = $id;
    $id400->loadMatchingObject($order);
    $id400->loadRefsFwd();
    return $id400->_ref_object;
  }
  
  
  function onStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    
    // Special pre-triggers
    if ($mbObject instanceof CSejour) {
      //	ALREADY DONE AS POST TRIGGER, SEE BELOW      
//      if ($mbObject->fieldModified("sortie_reelle")) {
//        CSpActesExporter::exportSejour($mbObject);
//      }
      $sejour = $mbObject;
      $sejour->loadRefPatient();
      $this->getIds400For($sejour->_ref_patient);
    }
    

    // Propagate modifications to other IDs
    foreach ($this->getIds400For($mbObject) as $id400) {      
      // Check if id has to be rebuilt
      if (!$this->checkId($mbObject, $id400->id400)) {
        $id400->id400 = $this->makeId($mbObject);
        $id400->last_update = mbDateTime();
	      if ($msg = $id400->store()) {
	        trigger_error("Error updating id400 for object  '$mbObject->_view' : $msg", E_USER_WARNING);
	        continue;
	      }
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
        $spInstance->changeDSN($group_id);
	      $spInstance->_id = $id400->id400;
	      if (!$spInstance->isConcernedBy($mbObject)) {
	        continue;
	      }

	      $spInstance->mapFrom($mbObject);
	      
	      // Propagated object
	      if ($msg = $spInstance->store()) {
	        trigger_error("Error propagating object '$spInstance->_class_name ($spInstance->_id)' : $msg", E_USER_WARNING);
	        continue;
	      }
      }
    }
    
    // Special post-triggers
    if ($mbObject instanceof CSejour) {
      if ($mbObject->fieldModified("sortie_reelle")) {
        CSpActesExporter::exportSejour($mbObject);
      }
    }
  }
  
  function onMerge(CMbObject &$mbObject) {
    $this->onStore($mbObject);
  }
  
  function onDelete(CMbObject &$mbObject) {
  }
}

?>