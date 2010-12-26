<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author Fabien Ménager
 */

$class_name = CValue::getOrSession('class_name', 'CPatient');
$objects_count = CValue::getOrSession('objects_count', 20);

$count = 0;
$zombies = array();
$object = new $class_name;
$installed_classes = CApp::getInstalledClasses();
$ds = $object->_spec->ds;

if ($object->_spec->table) {
  $object->makeAllBackSpecs();
  foreach ($object->_backSpecs as $name => $back_spec) {
    $back_object = new $back_spec->class;
    $fwd_spec = $back_object->_specs[$back_spec->field];

    // Check the back ref only if the class's module is installed
    if (!in_array($back_spec->class, $installed_classes)) {
    	continue;
		}
		
    $queryCount = "SELECT COUNT(*) AS total ";
    $queryLoad  = "SELECT `back_obj`.* ";
    
    $query = "FROM `{$back_object->_spec->table}` AS `back_obj`
      LEFT JOIN `{$object->_spec->table}` AS `obj` ON `obj`.`{$object->_spec->key}` = `back_obj`.`{$back_spec->field}`
      WHERE `obj`.`{$object->_spec->key}` IS NULL 
      AND `back_obj`.`{$back_spec->field}` IS NOT NULL";
    
		if ($field_meta = $fwd_spec->meta) {
      $query .= "\n AND `back_obj`.`$field_meta` = '$class_name'";
		}
    
    $queryCount .= $query;
    $queryLoad  .= $query;
    
    $row = $ds->fetchArray($ds->exec($queryCount));
    $zombies[$name] = array(
      'count' => $row['total'],
      'objects' => $back_object->loadQueryList($queryLoad, " LIMIT $objects_count"),
    );
  }
}

$smarty = new CSmartyDP();

$smarty->assign("object", $object);
$smarty->assign("zombies", $zombies);
$smarty->assign("classes", $installed_classes);
$smarty->assign("class_name", $class_name);
$smarty->assign("objects_count", $objects_count);

$smarty->display("check_zombie_objects.tpl");

?>