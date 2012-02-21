<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_class_id = CValue::getOrSession("ex_class_id");

$ex_class = new CExClass;
$ex_class->load($ex_class_id);

$class_tree = CExClass::getTree();

$counts = array();

foreach($class_tree as $_key => $_events) {
  $counts[$_key] = 0;
  foreach($_events as $_event) {
    $counts[$_key] += count($_event);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("class_tree", $class_tree);
$smarty->assign("counts", $counts);
$smarty->assign("ex_class", $ex_class);
$smarty->display("inc_list_ex_class.tpl");
