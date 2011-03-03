<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$class_tree = CExClass::getTree();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("class_tree", $class_tree);
$smarty->display("inc_list_ex_class.tpl");
