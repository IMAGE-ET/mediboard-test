<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$ex_class = new CExClass;
$list_ex_class = $ex_class->loadList(null, "host_class, event");

$class_tree = array();

foreach($list_ex_class as $_ex_class) {
	$host_class = $_ex_class->host_class;
  $event = $_ex_class->event;
	
	if (!isset($class_tree[$host_class])) {
		$class_tree[$host_class] = array();
	}
	
  if (!isset($class_tree[$host_class][$event])) {
    $class_tree[$host_class][$event] = array();
  }
	
	$class_tree[$host_class][$event][] = $_ex_class;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("class_tree", $class_tree);
$smarty->display("inc_list_ex_class.tpl");
