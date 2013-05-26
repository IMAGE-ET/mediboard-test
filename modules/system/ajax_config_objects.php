<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$classname = CValue::get("classname");

$objects = array(
  "default" => null,
  "objects" => array(),
);

$class = new $classname;
$class->loadRefObjectConfigs();

// Détermine si l'on a pas une config par défaut enregistrée
$object_class = $classname."Config";
$where = array();
$where["object_id"]    = " IS NULL";
$default = new $object_class;
$default->loadObject($where);
if ($default->_id) {
  $class->_ref_object_configs = $default;
}

$objects["default"] = $class;

foreach ($class->loadList() as $_object) {
  $_object->loadRefObjectConfigs();
  $objects["objects"][] = $_object;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("objects", $objects);
$smarty->display("inc_config_objects.tpl");
