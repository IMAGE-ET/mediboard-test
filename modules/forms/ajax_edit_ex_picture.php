<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$ex_picture_id = CValue::get("ex_picture_id");
$ex_group_id   = CValue::get("ex_group_id");

CExObject::$_locales_cache_enabled = false;

$ex_group = new CExClassFieldGroup();
$ex_group->load($ex_group_id);

$ex_picture = new CExClassPicture();

if ($ex_picture->load($ex_picture_id)) {
  $ex_picture->loadRefsNotes();
}
else {
  $ex_picture->ex_group_id = $ex_group_id;
}

$ex_picture->loadRefPredicate()->loadView();
$ex_picture->loadRefExClass();
$ex_picture->loadRefFile();

$smarty = new CSmartyDP();
$smarty->assign("ex_picture", $ex_picture);
$smarty->assign("ex_group", $ex_group);
$smarty->display("inc_edit_ex_picture.tpl");