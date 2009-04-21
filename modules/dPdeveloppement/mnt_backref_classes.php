<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author openXtrem
 */

global $can;

$can->needsRead();

$classes = getMbClasses();
$classes[] = "CMbObject";

// Looking for what is actually coded
$present = array();
foreach ($classes as $class) {
  $object = new $class;
  $object->makeAllBackSpecs();
  foreach ($object->_backSpecs as $backName => $backSpec) {
    if (!$backSpec->isInherited()) {
      $present[$class][$object->_backProps[$backName]] = $backName;
    }
  }
}

ksort($present);
// mbTrace($present);

// Looking for what should be coded
$wanted = array();
foreach ($classes as $class) {
  $object = new $class;
  if ($object->_spec->table) {
	  foreach ($object->_specs as $field => $spec) {
	    if ($field != $object->_spec->key && $field[0] != "_") {
		    if ($spec instanceof CRefSpec) {
		      // Cas des meta-object avec enum list de classes
		      if ($spec->meta) {
		        $meta_spec = $object->_specs[$spec->meta];
            if ($meta_spec instanceof CEnumSpec) {
              foreach ($meta_spec->_list as $_class) {
		            $wanted[$_class]["$class $field"] = true;
            	}
            }
            else {
		          $wanted[$spec->class]["$class $field"] = true;
            }
		      }
		      else {
	          $wanted[$spec->class]["$class $field"] = true;
		      }
		    }
	    }    
	  }
  }
}

ksort($wanted);
//mbTrace($wanted);

// Checking out the result
$class_name = mbGetValueFromGet("class_name");
$show       = mbGetValueFromGet("show", "all");

$reports = array();
foreach ($classes as $class) {
  if ($class_name && $class != $class_name) {
    continue;
  }
  
  // Are the wanted present ?
	if (isset($wanted[$class])) {
	  foreach ($wanted[$class] as $backProp => $backName) {
	    $reports[$class][$backProp] = @array_key_exists($backProp, $present[$class]) ? "ok"  : "wanted";
	  }
	}

  // Are the present wanted ?
	if (isset($present[$class])) {
	  foreach ($present[$class] as $backProp => $backName) {
	    $reports[$class][$backProp] = @array_key_exists($backProp, $wanted[$class]) ? "ok"  : "present";
	  }
	}
	
	if (isset($reports[$class]) && $show == "errors") {
		CMbArray::removeValue("ok", $reports[$class]);
	}
}

if ($show == "errors") {
	CMbArray::removeValue(array(), $reports);
}



//mbTrace($reports);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("class_name", $class_name);
$smarty->assign("show"      , $show);
$smarty->assign("classes", $classes);
$smarty->assign("present", $present);
$smarty->assign("wanted" , $wanted);
$smarty->assign("reports", $reports);

$smarty->display("mnt_backref_classes.tpl");

?>