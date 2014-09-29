<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$stock_location = new CProductStockLocation();

$group_id = CGroups::loadCurrent()->_id;

$classes = array(
  "CGroups"         => "Pharmacie",
  "CService"        => CAppUI::tr("CService"),
  "CBlocOperatoire" => CAppUI::tr("CBlocOperatoire"),
);

$ds = $stock_location->getDS();

$lists = array();

foreach ($classes as $_class => $_label) {
  $where = array(
    "product_stock_location.object_class" => $ds->prepare("=?", $_class),
  );
  $ljoin = array();

  switch ($_class) {
    case "CGroups":
      $where["product_stock_location.object_id"] = $ds->prepare("=?", $group_id);
      break;

    case "CService":
      $ljoin["service"] = "service.service_id = product_stock_location.object_id";
      $where["service.group_id"] = $ds->prepare("=?", $group_id);
      break;

    case "CBlocOperatoire":
      $ljoin["bloc_operatoire"] = "bloc_operatoire.bloc_operatoire_id = product_stock_location.object_id";
      $where["bloc_operatoire.group_id"] = $ds->prepare("=?", $group_id);
      break;
    
    default:
      //
  }

  $order = 'object_class, object_id, position, product_stock_location.name';

  $lists[$_class] = $stock_location->loadList($where, $order, null, null, $ljoin);
}


// Création du template
$smarty = new CSmartyDP();
$smarty->assign('lists', $lists);
$smarty->assign('classes', $classes);
$smarty->display('vw_idx_stock_location.tpl');

