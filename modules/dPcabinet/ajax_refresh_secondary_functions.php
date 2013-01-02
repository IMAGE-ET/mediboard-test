<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$chir_id    = CValue::get("chir_id");
$field_name = CValue::get("field_name", "_secondary_function_id");
$empty_function_principale = CValue::get("empty_function_principale", 0);
$type_onchange = CValue::get("type_onchange", "consult");

$chir = new CMediusers();
$chir->load($chir_id);
$chir->loadRefFunction();

$_functions = $chir->loadBackRefs("secondary_functions");

$smarty = new CSmartyDP();

$smarty->assign("_functions", $_functions);
$smarty->assign("chir"      , $chir);
$smarty->assign("field_name", $field_name);
$smarty->assign("empty_function_principale", $empty_function_principale);
$smarty->assign("type_onchange", $type_onchange);

$smarty->display("inc_refresh_secondary_functions.tpl");
