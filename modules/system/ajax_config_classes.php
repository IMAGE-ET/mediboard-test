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
