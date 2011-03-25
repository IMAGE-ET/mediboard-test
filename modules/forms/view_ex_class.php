<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$ex_class_id = CValue::getOrSession("ex_class_id");

$ex_class = new CExClass;
$ex_class->load($ex_class_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_class", $ex_class);
$smarty->display("view_ex_class.tpl");
