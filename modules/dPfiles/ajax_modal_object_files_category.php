<?php 

/**
 * $Id$
 *  
 * @category dPfiles
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user = CUser::get();

$object_guid = CValue::get("object_guid");

$object = CMbObject::loadFromGuid($object_guid);

$category = new CFilesCategory();
$category->eligible_file_view = 1;
$categories = $category->loadMatchingList();

$nb_unread = 0;
foreach ($categories as $_cat) {
  $file = new CFile();
  $file->file_category_id = $_cat->_id;
  $file->setObject($object);
  /** @var CFile[] $files */
  $_cat->_ref_files = $file->loadMatchingList();

  foreach ($_cat->_ref_files as $file_id => $_file) {
    $_file->loadRefReadStatus($user->_id);
    if (!$_file->_ref_read_status->_id) {
      $nb_unread ++;
    }
    else {
      unset($_cat->_ref_files[$file_id]);
      continue;
    }
  }
}

// smarty
$smarty = new CSmartyDP();
$smarty->assign("nb_unread", $nb_unread);
$smarty->assign("user_id", $user->_id);
$smarty->assign("file_view", new CFileUserView());
$smarty->assign("categories", $categories);
$smarty->assign("object", $object);
$smarty->display("inc_list_object_files_category.tpl");