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

$objects_id    = CValue::post('_objects_id'); // array
$objects_class = CValue::post('_objects_class');


$objects = array();

if (class_exists($objects_class)) {
  $result = new $objects_class;
  $do = new CDoObjectAddEdit($objects_class, $result->_spec->key);
  
  // Création du nouveau patient
  if (intval(CValue::post("del"))) {
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
  if ($msg = $do->_obj->merge($objects,  CValue::post("fast"))) {
    $do->errorRedirect($msg);
  }

  $do->doRedirect();
}

?>