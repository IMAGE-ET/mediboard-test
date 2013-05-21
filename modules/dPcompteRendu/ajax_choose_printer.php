<?php

/**
 * Liste des imprimantes en réseau
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$mode_etiquette = CValue::get("mode_etiquette", 0);
$object_class   = CValue::get("object_class");
$object_id      = CValue::get("object_id");
$modele_etiquette_id = CValue::get("modele_etiquette_id");

$current_user = CAppUI::$user;
$function_id  = $current_user->function_id;

$printer = new CPrinter();
$where = array("function_id" => "= '$function_id'");
$printers = $printer->loadlist($where);

CMbObject::massLoadFwdRef($printers, "object_id", "CFunctions");

/** @var $printers CPrinter[] */
foreach ($printers as $_printer) {
  $_printer->loadTargetObject();
}

$smarty = new CSmartyDP();

$smarty->assign("mode_etiquette"     , $mode_etiquette);
$smarty->assign("printers"           , $printers);
$smarty->assign("object_class"       , $object_class);
$smarty->assign("object_id"          , $object_id);
$smarty->assign("modele_etiquette_id", $modele_etiquette_id);

$smarty->display("inc_choose_printer.tpl");