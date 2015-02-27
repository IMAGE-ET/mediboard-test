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

CCanDo::checkEdit();

$draw_id      = CValue::get('id');
$context_guid = CValue::get("context_guid");

$draw = new CFile();
$draw->load($draw_id);
$draw->loadRefsNotes();
$draw->loadRefAuthor();
$draw->loadRefsNotes();
$draw->getBinaryContent();

$user = CMediusers::get();
$user->loadRefFunction();
$functions = $user->loadRefsSecondaryFunctions();
$admin = $user->isAdmin();

$files_in_context = array();
$object = null;
if ($context_guid) {
  $object= CMbObject::loadFromGuid($context_guid);
  if ($object->_id) {
    $object->loadRefsFiles();
    foreach ($object->_ref_files as $file_id => $_file) {
      if ( (strpos($_file->file_type, "image/") === false) || ($_file->file_type == "image/fabricjs") || ($_file->annule) ) {
        unset($object->_ref_files[$file_id]);
      }
    }
  }
}

// creation
if (!$draw->_id) {

  // author = self
  $draw->author_id = $user->_id;
  $draw->_ref_author = $user;
  $draw->file_type = "image/svg+xml";
  $draw->file_name  = "Sans titre";

  // context
  if ($object && $object->_id) {
    $draw->setObject($object);
  }
  // assign to user
  else {
    $draw->setObject($user);
  }

  $draw->loadTargetObject();
}


$file_categories = CFilesCategory::listCatClass($draw->_class);

$category = new CDrawingCategory();
$where = array("user_id" => " = '$user->_id'");
$categories_user = $category->loadList($where);
$where = array("function_id" => " = '$user->function_id'");
$categories_function = $category->loadList($where);
$where = array("group_id" => " = '$user->_ref_function->group_id'");
$categories_group = $category->loadList($where);
/** @var CDrawingCategory[] $categories */
$categories = $categories_user + $categories_function + $categories_group;
foreach($functions as $_function) {
  $where = array("function_id" => " = '$_function->_id'");
  $categories_function = $category->loadList($where);
  $categories = array_merge($categories, $categories_function);
}

foreach ($categories as $_category) {
  $_category->countFiles();
}


//smarty
$smarty = new CSmartyDP();
$smarty->assign("admin", $admin);
$smarty->assign("draw", $draw);
$smarty->assign("categories", $categories);
$smarty->assign("file_categories", $file_categories);
$smarty->assign("object", $object);
$smarty->display("inc_vw_draw.tpl");