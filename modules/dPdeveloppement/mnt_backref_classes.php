<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author openXtrem
 */

CCanDo::checkRead();

// Checking out the result
$class = CValue::get("class");
$show       = CValue::get("show", "errors");

$classes = CApp::getMbClasses();
$classes[] = "CMbObject";

// Looking for what is actually coded
$present = array();
foreach ($classes as $_class) {
  $object = new $_class;
  $object->makeAllBackSpecs();
  foreach ($object->_backSpecs as $backName => $backSpec) {
    if (!$backSpec->isInherited()) {
      $present[$_class][$object->_backProps[$backName]] = $backName;
    }
  }
}

ksort($present);
//mbTrace($present, "Present");

// Looking for what should be coded
$wanted = array();
foreach ($classes as $_class) {
  $object = new $_class;
  if ($object->_spec->table) {
    foreach ($object->_specs as $field => $spec) {
      if ($field != $object->_spec->key && $field[0] != "_") {
        if ($spec instanceof CRefSpec) {
          // Cas des meta-object avec enum list de classes
          if ($spec->meta) {
            $meta_spec = $object->_specs[$spec->meta];
            if ($meta_spec instanceof CEnumSpec) {
              foreach ($meta_spec->_list as $_class_meta) {
                $wanted[$_class_meta]["$_class $field"] = true;
              }
            }
            else {
              $wanted[$spec->class]["$_class $field"] = true;
            }
          }
          else {
            $wanted[$spec->class]["$_class $field"] = true;
          }
        }
      }    
    }
  }
}

ksort($wanted);
//mbTrace($wanted, "Wanted");

$reports = array();
$error_count = 0;
foreach ($classes as $_class) {
  if ($class && $_class != $class) {
    continue;
  }
  
  // Are the wanted present ?
  if (isset($wanted[$_class])) {
    foreach ($wanted[$_class] as $backProp => $backName) {
      $correct = @array_key_exists($backProp, $present[$_class]);
      $error_count += $correct ? 0 : 1;
      $reports[$_class][$backProp] =  $correct ? "ok"  : "wanted";
    }
  }

  // Are the present wanted ?
  if (isset($present[$_class])) {
    foreach ($present[$_class] as $backProp => $backName) {
      $correct = @array_key_exists($backProp, $wanted[$_class]);
      $error_count += $correct ? 0 : 1;
      $reports[$_class][$backProp] =  $correct ? "ok"  : "present";
    }
  }
  
  if (isset($reports[$_class]) && $show == "errors") {
    CMbArray::removeValue("ok", $reports[$_class]);
  }
}

if ($show == "errors") {
  CMbArray::removeValue(array(), $reports);
}

//mbTrace($reports);
//mbTrace($error_count);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("class", $class);
$smarty->assign("show" , $show);
$smarty->assign("classes", $classes);
$smarty->assign("present", $present);
$smarty->assign("wanted" , $wanted);
$smarty->assign("reports", $reports);
$smarty->assign("error_count", $error_count);

$smarty->display("mnt_backref_classes.tpl");

?>