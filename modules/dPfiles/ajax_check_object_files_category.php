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

CCanDo::checkRead();

$user = CUser::get();

$object_class = CValue::get("object_class");
$object_ids   = CValue::get("object_ids");

$object_ids = explode("-", $object_ids);
CMbArray::removeValue("", $object_ids);

if (empty($object_ids)) {
  CApp::json(array());

  CApp::rip();
}

$category = new CFilesCategory();
$category->eligible_file_view = 1;
$categories = $category->loadMatchingList();

$nb_unread = array();

foreach ($object_ids as $_object_id) {
  $_nb_unread = 0;

  foreach ($categories as $_cat) {
    $file      = new CFile();
    $file->file_category_id = $_cat->_id;
    $file->object_class     = $object_class;
    $file->object_id        = $_object_id;

    /** @var CFile[] $files */
    $files = $file->loadMatchingList();
    foreach ($files as $_file) {
      if (!$_file->getPerm(PERM_READ)) {
        continue;
      }

      $_file->loadRefReadStatus($user->_id);
      if (!$_file->_ref_read_status->_id) {
        $_nb_unread ++;
      }
    }
  }

  $nb_unread["$object_class-$_object_id"] = $_nb_unread;
}

CApp::json($nb_unread);
CApp::rip();