<?php 

/**
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user = CMediusers::get();
$gallery = CValue::get("gallery", 0);

$file = new CFile();
$where = array();
$where["author_id"] = " = '$user->_id'";
$where["file_type"] = " = 'image/fabricjs'";
$where["object_class"] = " != 'CDrawingCategory'";
/** @var CFile[] $files */
$files = $file->loadList($where, "file_date DESC");
foreach ($files as $_file) {
  $_file->loadTargetObject();
  $_file->updateFormFields();
  $_file->loadRefAuthor()->loadRefFunction();
}

$where["file_type"] = " LIKE '%svg%' ";
$files_svg = $file->loadList($where, "file_date DESC");
foreach ($files_svg as $_file) {
  $_file->loadTargetObject();
}


//smarty
$smarty = new CSmartyDP();
$smarty->assign("user", $user);
$smarty->assign("gallery", $gallery);
$smarty->assign("files", $files);
$smarty->assign("files_svg", $files_svg);
$smarty->display("vw_drawings.tpl");