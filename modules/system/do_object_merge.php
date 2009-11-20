<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m;
$can->needsEdit();

$objects_id     = CValue::post("_objects_id"); // array
$objects_class  = CValue::post("_objects_class");
$base_object_id = CValue::post("_base_object_id");
$del            = CValue::post("del");
$fast           = CValue::post("fast");

// If the class is valid
if (class_exists($objects_class)) {
  $objects = array();
  $do = new CDoObjectAddEdit($objects_class);
  
  // If alt mode, load the specified object
  if ($base_object_id) {
    $do->_obj->load($base_object_id);
  }
  
  // Création du nouveau patient
  if (intval($del)) {
    $do->errorRedirect("Fusion en mode suppression impossible");
  }
  
  // Unset the base_object from the list
  if ($do->_obj->_id) {
    foreach ($objects_id as $key => $object_id) {
      if ($do->_obj->_id == $object_id) {
        unset ($objects_id[$key]);
      }
    }
    // Only one objet to merge
    $objects_id = array(reset($objects_id));
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
  if ($msg = $do->_obj->merge($objects, $fast)) {
    $do->errorRedirect($msg);
  }

  $do->doRedirect();
}

?>