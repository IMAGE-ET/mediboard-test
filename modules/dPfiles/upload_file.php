<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$object = mbGetObjectFromGetOrSession("object_class", "object_id", "object_guid");

$file_category_id = CValue::getOrSession("file_category_id");
$_rename          = CValue::getOrSession("_rename");
$uploadok         = CValue::get("uploadok");
$private          = CValue::get("private");
$named            = CValue::get("named");

$listCategory = CFilesCategory::listCatClass($object->_class);

$file = new CFile();
$file->private = $private;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object"          , $object);
$smarty->assign("file_category_id", $file_category_id);
$smarty->assign("uploadok"        , $uploadok);
$smarty->assign("listCategory"    , $listCategory);
$smarty->assign("_rename"         , $_rename);
$smarty->assign("named"           , $named);
$smarty->assign("file"            , $file);
$smarty->display("upload_file.tpl");
