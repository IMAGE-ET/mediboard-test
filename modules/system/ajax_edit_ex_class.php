<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$ex_class_id = CValue::getOrSession("ex_class_id");

$ex_class = new CExClass;
$ex_class->load($ex_class_id);
$ex_class->loadRefsFields();
$ex_class->loadRefsConstraints();
$ex_class->loadRefsNotes();

foreach($ex_class->_ref_fields as $_ex_field) {
  $_ex_field->getSpecObject();
}

foreach($ex_class->_ref_constraints as $_ex_constraint) {
  $_ex_constraint->loadRefExClass();
}

$classes = array();

if (!$ex_class->_id) {
  $classes = CApp::getMbClasses(array(), $instances);
  
  foreach($instances as $class => $instance) {
    if (empty($instance->_spec->events)) {
      unset($instances[$class]);
      continue;
    }
    
    $instances[$class] = $instance->_spec->events;
  }
  
  $classes = $instances;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_class", $ex_class);
$smarty->assign("classes", $classes);
$smarty->display("inc_edit_ex_class.tpl");