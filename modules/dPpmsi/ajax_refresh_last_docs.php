<?php

/**
 * maternite
 *  
 * @category dPpmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$cat_docs        = CValue::getOrSession("cat_docs");
$specialite_docs = CValue::getOrSession("specialite_docs");
$prat_docs       = CValue::getOrSession("prat_docs");
$date_docs_min   = CValue::getOrSession("date_docs_min");
$date_docs_max   = CValue::getOrSession("date_docs_max");
$page            = CValue::get("page");

$docs  = array();
$where = array();
$ljoin = array();
$cr    = new CCompteRendu;
$long_period = mbDaysRelative($date_docs_min, $date_docs_max) > 10;

$total_docs = 0;

if (($cat_docs || $specialite_docs || $prat_docs || ($date_docs_min && $date_docs_max)) && !$long_period) {
  $where["compte_rendu.object_class"] = " IN ('COperation', 'CSejour', 'CPatient')";
  
  if ($cat_docs) {
    $where["file_category_id"] = " = '$cat_docs'";
  }
  
  if ($date_docs_min && $date_docs_max) {
    $ljoin["user_log"] = "compte_rendu.compte_rendu_id = user_log.object_id AND user_log.object_class = 'CCompteRendu' AND user_log.type = 'create'";
    $where["user_log.date"] = "BETWEEN '$date_docs_min 00:00:00' AND '$date_docs_max 23:59:59'";
  }
  
  if ($prat_docs) {
    $where["author_id"] = " = '$prat_docs'";
  }
  else if ($specialite_docs) {
    if (!isset($ljoin["user_log"])) {
      $ljoin["user_log"] = "compte_rendu.compte_rendu_id = user_log.object_id AND user_log.object_class = 'CCompteRendu' AND user_log.type = 'create'";
    }
    $ljoin["users_mediboard"] = "user_log.user_id = users_mediboard.user_id";
    $where["users_mediboard.function_id"] = " = '$specialite_docs'";
  }
  $total_docs = $cr->countList($where, null, $ljoin);
  $docs = $cr->loadList($where, "date desc", "$page, 30", null, $ljoin);
  
  foreach ($docs as $_doc) {
    $_doc->_date = $_doc->loadFirstLog()->date;
  }
}

$smarty = new CSmartyDP;

$smarty->assign("cat_docs", $cat_docs);
$smarty->assign("specialite_docs", $specialite_docs);
$smarty->assign("prat_docs", $prat_docs);
$smarty->assign("date_docs_min", $date_docs_min);
$smarty->assign("date_docs_max", $date_docs_max);
$smarty->assign("docs"     , $docs);
$smarty->assign("long_period", $long_period);
$smarty->assign("page"     , $page);
$smarty->assign("total_docs", $total_docs);
$smarty->display("inc_refresh_last_docs.tpl");
