<?php 

/**
 * $Id$
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$source_guid       = CValue::get("source_guid");
$current_directory = CValue::get("current_directory");

$max_size = ini_get("upload_max_filesize");
$source = CMbObject::loadFromGuid($source_guid);

//template
$smarty = new CSmartyDP();
$smarty->assign("source_guid"      , $source_guid);
$smarty->assign("current_directory", $current_directory);
$smarty->assign("max_size"         , $max_size);

$smarty->display("inc_add_file.tpl");