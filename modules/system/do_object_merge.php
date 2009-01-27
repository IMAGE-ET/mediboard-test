<?php /* $Id: do_patients_fusion.php 5425 2008-12-15 15:18:18Z rhum1 $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: 5425 $
* @author Fabien M�nager
*/

global $can, $m;
$can->needsEdit();

$objects_id    = mbGetValueFromPost('_objects_id'); // array
$objects_class = mbGetValueFromPost('_objects_class');


$objects = array();

if (class_exists($objects_class)) {
  $result = new $objects_class;
  $do = new CDoObjectAddEdit($objects_class, $result->_spec->key);
  
  // Cr�ation du nouveau patient
  if (intval(mbGetValueFromPost("del"))) {
    $do->errorRedirect("Fusion en mode suppression impossible");
  }
  
  foreach ($objects_id as $object_id) {
    $object = new $objects_class;
    
    // the CMbObject is loaded
    if (!$object->load($object_id)){
      $do->errorRedirect("Chargement impossible de l'objet [$object_id]");
      continue;
    }
    $objects[] = $object;
  }
  
  // the result data is binded to the new CMbObject
  $do->doBind();

  // the objects are merged with the result
  if ($msg = $do->_obj->merge($objects,  mbGetValueFromPost("fast"))) {
    $do->errorRedirect($msg);
  }

  $do->doRedirect();
}

?>