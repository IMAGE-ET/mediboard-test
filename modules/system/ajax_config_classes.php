<?php /* $Id: ajax_test_dsn.php 6069 2009-04-14 10:17:11Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 6069 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$module = CValue::get("module");

$classes = array();
foreach (CModule::getClassesFor($module) as $_class) {
  $class = new $_class;
  $props = $class->_backProps;
  if (!array_key_exists("object_configs", $props)) {
    continue;
  }
  
  $classes[] = $class;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("classes", $classes);
$smarty->display("inc_config_classes.tpl");
?>