<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author Fabien Ménager
 */

$class_name = mbGetValueFromGetOrSession('class_name', 'CPatient');
$objects_count = mbGetValueFromGetOrSession('objects_count', 20);

$count = 0;
$zombies = array();
$object = new $class_name;
$installed_classes = getInstalledClasses();
$ds = $object->_spec->ds;

if ($object->_spec->table) {
	$object->makeAllBackSpecs();
	foreach($object->_backSpecs as $name => $back_spec) {
		$back_object = new $back_spec->class;

    // Check the back ref only if the class's module is installed
    if (!in_array($back_spec->class, $installed_classes)) continue;
		
    $sqlCount = "SELECT COUNT(*) AS total ";
		$sqlLoad  = "SELECT `back_obj`.* ";
		
    $sql = "FROM `{$back_object->_spec->table}` AS `back_obj`
						LEFT JOIN `{$object->_spec->table}` AS `obj` ON `obj`.`{$object->_spec->key}` = `back_obj`.`{$back_spec->field}`
						WHERE 
						  `obj`.`{$object->_spec->key}` IS NULL AND 
							`back_obj`.`{$back_spec->field}` IS NOT NULL";
		
		if (is_subclass_of($back_object, 'CMbMetaObject')) {
			$sql .= " AND `back_obj`.`object_class` = '$class_name'";
		}
		
		$row = $ds->fetchArray($ds->exec($sqlCount.$sql));
		$zombies[$name] = array(
		  'count' => $row['total'],
			'objects' => $back_object->loadQueryList($sqlLoad.$sql." LIMIT $objects_count"),
		);
	}
}

$smarty = new CSmartyDP();

$smarty->assign("zombies", $zombies);
$smarty->assign("classes", $installed_classes);
$smarty->assign("class_name", $class_name);
$smarty->assign("objects_count", $objects_count);

$smarty->display("check_zombie_objects.tpl");

?>