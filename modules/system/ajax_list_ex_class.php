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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_ex_class", $list_ex_class);
$smarty->display("inc_list_ex_class.tpl");
