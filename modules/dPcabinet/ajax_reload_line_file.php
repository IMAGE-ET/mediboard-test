<?php 

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$file_id = CValue::get("id");
$name_readonly = CValue::get("name_readonly", 0);

$file = new CFile;
$file->load($file_id);
$file->canDo();

$object_id = CValue::get("object_id");
$object_class = CValue::get("object_class");

$smarty = new CSmartyDP();
$smarty->assign("_file", $file);
$smarty->assign("object_id", $object_id);
$smarty->assign("object_class", $object_class);
$smarty->assign("name_readonly", $name_readonly);

$smarty->display("inc_widget_line_file.tpl");
