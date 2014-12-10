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

CCanDo::checkRead();

$ex_class_id  = CValue::get("ex_class_id");
$ex_object_id = CValue::get("ex_object_id");

if (!$ex_class_id) {
  $msg = "Impossible d'afficher le formulaire sans connaître la classe de base";
  CAppUI::stepAjax($msg, UI_MSG_WARNING);
  trigger_error($msg, E_USER_ERROR);
  return;
}

$ex_object = new CExObject($ex_class_id);
$ex_object->load($ex_object_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("ex_object", $ex_object);
$smarty->display("view_ex_object.tpl");
