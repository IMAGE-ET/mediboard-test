<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$where = array("ex_class_id" => "IS NULL"); 

$ex_concept = new CExClassField;
$list_ex_concept = $ex_concept->loadList($where, "name");

CMbArray::invoke($list_ex_concept, "updateTranslation");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("list_ex_concept", $list_ex_concept);
$smarty->display("inc_list_ex_concept.tpl");
