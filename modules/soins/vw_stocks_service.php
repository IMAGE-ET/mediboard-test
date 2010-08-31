<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage soins
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $m;
CCanDo::checkEdit();

$service_id          = CValue::getOrSession('service_id');
$start               = CValue::getOrSession('start', 0);
$only_service_stocks = CValue::getOrSession('only_service_stocks', 1);
$only_common         = CValue::getOrSession('only_common', 1);
$endowment_id        = CValue::getOrSession('endowment_id');
$keywords            = CValue::getOrSession('keywords');

$date_min = CValue::getOrSession('_date_min', mbDate("-1 DAY"));
$date_max = CValue::getOrSession('_date_max', mbDate());

CValue::setSession('_date_min', $date_min);
CValue::setSession('_date_max', $date_max);

// Services list
$service = new CService();
$list_services = $service->loadListWithPerms(PERM_READ);

if ($m == "dPurgences") {
  foreach($list_services as $_id => $_service) {
    if (!$_service->urgence) {
      unset($list_services[$_id]);
    }
  } 
}

$delivrance = new CProductDelivery();
$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;

if (count($list_services) == 0) {
  CAppUI::stepMessage(UI_MSG_WARNING, "Vous n'avez accès à aucun service pour effectuer des commandes");
  return;
}

// Création du template
$smarty = new CSmartyDP("modules/soins");

$smarty->assign('service_id',    $service_id);
$smarty->assign('list_services', $list_services);
$smarty->assign('delivrance',    $delivrance);
$smarty->assign('start',         $start);
$smarty->assign('only_service_stocks', $only_service_stocks);
$smarty->assign('keywords',      $keywords);
$smarty->assign('only_common',   $only_common);
$smarty->assign('endowment_id',  $endowment_id);

$smarty->display('vw_stocks_service.tpl');
