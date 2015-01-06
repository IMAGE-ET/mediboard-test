<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Bloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkAdmin();
$purge = CView::get("purge", "bool default|0");
$max   = CView::get("max"  , "num default|100");

$group = CGroups::loadCurrent();
$where = array();
$where["bloc_operatoire.group_id"] = "= '$group->_id'";
$where[] = "
  NOT EXISTS (
    SELECT * FROM `sallesbloc`
    WHERE `sallesbloc`.`bloc_id` = `bloc_operatoire`.`bloc_operatoire_id`
  )
";
$order = "bloc_operatoire.nom";

$bloc = new CBlocOperatoire();
$success_count = 0;
$failures = array();
$blocs = array();
if ($purge) {
  /** @var CBlocOperatoire[] $blocs */
  $blocs = $bloc->loadList($where, $order, $max);
  foreach ($blocs as $_bloc) {
    $back_props = 0;
    $back_props += $_bloc->countBackRefs('salles');
    $back_props += $_bloc->countBackRefs('check_lists');
    $back_props += $_bloc->countBackRefs('stock_locations');
    $back_props += $_bloc->countBackRefs('postes');
    $back_props += $_bloc->countBackRefs('check_list_categories');
    $back_props += $_bloc->countBackRefs('check_list_type_links');
    $back_props += $_bloc->countBackRefs('product_address_orders');
    $back_props += $_bloc->countBackRefs('origine_brancardage');
    $back_props += $_bloc->countBackRefs('origine_item');

    if (!$back_props) {
      if ($msg = $_bloc->delete()) {
        $failures[$_bloc->_id] = $msg;
        continue;
      }
      $success_count++ ;
    }
  }
}

$count = $bloc->countList($where);

$smarty = new CSmartyDP();

$smarty->assign("blocs", $blocs);
$smarty->assign("purge", $purge);
$smarty->assign("max"  , $max);
$smarty->assign("count", $count);
$smarty->assign("success_count" , $success_count);
$smarty->assign("failures"      , $failures);

$smarty->display("purge_empty_blocsop.tpl");