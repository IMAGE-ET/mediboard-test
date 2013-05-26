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

$current_directory = CValue::get("current_directory");
$delete            = CValue::get("delete", false);
$rename            = CValue::get("rename", false);
$new_name          = CValue::get("new_name");
$file              = CValue::get("file");
$source_guid       = CValue::get("source_guid");

/** @var CSourceFTP $source */
$source = CMbObject::loadFromGuid($source_guid);

if ($delete && $file) {
  $source->delFile($file, $current_directory);
}

if ($rename && $new_name) {
  $source->renameFile($file, $new_name, $current_directory);
}

$current_directory = $source->getCurrentDirectory($current_directory);
$files             = $source->getListFilesDetails($current_directory);

$smarty = new CSmartyDP();

$smarty->assign("files"            , $files);
$smarty->assign("current_directory", $current_directory);
$smarty->assign("source_guid"      , $source_guid);

$smarty->display("inc_manage_file.tpl");