<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

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
$smarty->display("inc_list_ex_class.tpl");
