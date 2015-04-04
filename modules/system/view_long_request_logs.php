<?php 

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();
CView::enforceSlave();

$filter = new CLongRequestLog();

$filter->_date_min = CValue::get("_date_min", CMbDt::date("-1 MONTH") . ' 00:00:00');
$filter->_date_max = CValue::get("_date_max");
$filter->user_id   = CValue::get("user_id");

// Récupération de la liste des utilisateurs disponibles
$user = new CUser;
$user->template = "0";
$order = "user_last_name, user_first_name";
$user_list = $user->loadMatchingList($order);

$smarty = new CSmartyDP();
$smarty->assign("user_list",  $user_list);
$smarty->assign("filter",     $filter);
$smarty->display("view_long_request_logs.tpl");