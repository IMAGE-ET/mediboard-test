<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI, $can, $m, $g;

$can->needsAdmin();

if (null == $pass = mbGetValueFromGet("pass")) {
  $AppUI->stepAjax("Fonctionnalité désactivée car trop dangereuse.", UI_MSG_WARNING);
  return;
}

if (md5($pass) != "aa450aff6d0f4974711ff4c5536ed4cb") {
  $AppUI->stepAjax("Mot de passe incorrect.\nAttention, fonctionnalité à utiliser avec une extrême prudence", UI_MSG_ERROR);
}


/**
 * Purge objects of a given class linked to an idSante400 from database
 * via a direct SQL query, no framework for better performance
 * @param string $className The class of objects to be removed
 * @param string $fakeClassName The fake class name to handle the CUser case
 */
function purgeObjects($className, $fakeClassName = null) {
  global $AppUI;
  $object = new $className;
  if ($object instanceof CMbObject) {
    $AppUI->stepAjax("Impossible de purger des objets de la classe '$className'", UI_MSG_ERROR);
  }
    
  // Ugly hack for CUser case
  $classNameID = mbGetValue($fakeClassName, $className);
  
  $query = "DELETE FROM `{$object->_spec->table}`" .
      "\nWHERE `{$object->_spec->key}`" .
      "\nIN (" .
      "\n  SELECT object_id" .
      "\n  FROM `id_sante400`" .
      "\n  WHERE `object_class` = '$classNameID')";
  
  $ds = CSQLDataSource::get("std");
  $ds->exec($query);
  $countObjects = $ds->affectedRows();
  
  $AppUI->stepAjax("$countObjects objets de type '$className' purgés");

  if (!$fakeClassName) {
    $query = "DELETE FROM `id_sante400`" .
      "\n  WHERE `object_class` = '$classNameID'";
    $ds->exec($query);
    $countIDs = $ds->affectedRows();

    $AppUI->stepAjax("$countIDs identifiants purgés pour le type '$className'");
  }  
}

purgeObjects("CGroups");
purgeObjects("CFunctions");
purgeObjects("CUser", "CMediusers");
purgeObjects("CMediusers");
purgeObjects("CPatient");
purgeObjects("CSejour");
purgeObjects("COperation");
purgeObjects("CActeCCAM");
purgeObjects("CNaissance");

?>