<?php 
/**
 * View stats EAI
 * 
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$count             = CValue::getOrSession("count", 30);
$date_production   = CValue::getOrSession("date_production", mbDate());
$group_id          = CValue::getOrSession("group_id", CGroups::loadCurrent()->_id);

$filter = new CExchangeDataFormat();
$filter->date_production = $date_production;
$filter->group_id = $group_id;

$exchanges_classes = array();
foreach (CExchangeDataFormat::getAll() as $key => $_exchange_class) {
  foreach (CApp::getChildClasses($_exchange_class, array(), true) as $_child_key => $_child_class) {
    $exchanges_classes[$_exchange_class][] = $_child_class;
  }
  if ($_exchange_class == "CExchangeAny") {
    $exchanges_classes[$_exchange_class][] = $_exchange_class;
  }
}

$criteres = array(
  'no_date_echange',
  'emetteur',
  'destinataire',
  'message_invalide',
  'acquittement_invalide',
);

$smarty = new CSmartyDP();

$smarty->assign("count", $count);
$smarty->assign("date_production", $date_production);
$smarty->assign("filter", $filter);
$smarty->assign("exchanges_classes", $exchanges_classes);
$smarty->assign("criteres", $criteres);

$smarty->display("vw_stats.tpl");
?>