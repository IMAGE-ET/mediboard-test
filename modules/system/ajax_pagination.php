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

$smarty = new CSmartyDP();
$smarty->assign("current", CValue::get("page", 0));
$smarty->assign("step", CValue::getOrSession("step"));
$smarty->assign("total", CValue::getOrSession("total"));
$smarty->assign("change_page", CValue::getOrSession("change_page"));
$smarty->display("inc_pagination.tpl");
