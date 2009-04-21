<?php

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Sherpa
 * @abstract Retrieve complete properties for a Sherpa MbObject 
 * as an XML file
 */

global $can;
$can->needsRead();

$response = array();

$filter = new CIdSante400();
$filter->id400        = mbGetValueFromGet("id400");
$filter->object_class = mbGetValueFromGet("object_class");

try {
  if (null == $class = $filter->object_class) {
    throw new Exception("No object class to query", 1);
  }
  
	if (!in_array($class, CSpObjectHandler::$queriable)) {
    throw new Exception("Object of class '$class' is not queriable", 2);
	}
    
  $object = CSpObjectHandler::getMbObjectFor($class, $filter->id400);
  if (!$object->_id) {
    throw new Exception("Object of class '$class' could not be found with id400 '$filter->id400'", 3);
  }
  
  $object->loadRefsFwd();
  
  foreach (array_keys($object->_specs) as $propName) {
    $response[$propName] = $object->$propName;
  }
}
catch (Exception $e) {
  $response["error_code"   ] = $e->getCode();
  $response["error_message"] = $e->getMessage();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"   , $filter);
$smarty->assign("response" , $response);
$smarty->assign("queriable", CSpObjectHandler::$queriable);

$smarty->display("object_properties.tpl");
?>