<?php

/**
 * Liste d'aides à la saisie
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

CCanDO::checkRead();

// Utilisateur sélectionné ou utilisateur courant
$filter_user_id = CValue::getOrSession("filter_user_id");
$filter_class   = CValue::getOrSession("filter_class");
$start          = CValue::getOrSession("start");
$keywords       = CValue::getOrSession("keywords");
$order_col      = CValue::getOrSession("order_col");
$order_way      = CValue::getOrSession("order_way");
$aide_id        = CValue::getOrSession("aide_id", "0");

$order_by = $order_col . " " . $order_way;

$userSel = new CMediusers();
$userSel->load($filter_user_id);

if (!$userSel->_id) {
  $userSel = CMediusers::get();
}

$userSel->loadRefFunction()->loadRefGroup();

if ($userSel->isPraticien()) {
  CValue::setSession("filter_user_id", $userSel->user_id);
  $filter_user_id = $userSel->user_id;
}

$where = array();
if ($filter_class) {
  $where["class"] = "= '$filter_class'";
}

// Liste des aides pour le praticien
$aides = array(
  "user" => array(),
  "func" => array(),
  "etab" => array(),
);

$aidesCount = array(
  "user" => 0,
  "func" => 0,
  "etab" => 0,
);

if ($userSel->user_id) {
  $_aide = new CAideSaisie();
  
  $where["user_id"] = "= '$userSel->user_id'";
  $aides["user_ids"] = array_keys($_aide->seek($keywords, $where, 1000));
  $aides["user"] = $_aide->seek($keywords, $where, $start["user"].", 30", true, null, $order_by);
  $aidesCount["user"] = $_aide->_totalSeek;
  foreach ($aides["user"] as $aide) {
    $aide->loadRefsFwd();
  }
  unset($where["user_id"]);

  $where["function_id"] = "= '$userSel->function_id'";
  $aides["func_ids"] = array_keys($_aide->seek($keywords, $where, 1000));
  $aides["func"] = $_aide->seek($keywords, $where, $start["func"].", 30", true, null, $order_by);
  $aidesCount["func"] = $_aide->_totalSeek;
  foreach ($aides["func"] as $aide) {
    $aide->loadRefsFwd();
  }
  unset($where["function_id"]);

  $where["group_id"] = "= '{$userSel->_ref_function->group_id}'";
  $aides["etab_ids"] = array_keys($_aide->seek($keywords, $where, 1000));
  $aides["etab"] = $_aide->seek($keywords, $where, $start["etab"].", 30", true, null, $order_by);
  $aidesCount["etab"] = $_aide->_totalSeek;
  foreach ($aides["etab"] as $aide) {
    $aide->loadRefsFwd();
  }
  unset($where["group_id"]);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("userSel",      $userSel);
$smarty->assign("aides",        $aides);
$smarty->assign("aidesCount",   $aidesCount);
$smarty->assign("filter_class", $filter_class);
$smarty->assign("start",        $start);
$smarty->assign("order_col",    $order_col);
$smarty->assign("order_way",    $order_way);
$smarty->assign("aide_id",      $aide_id);

$smarty->display("inc_tabs_aides.tpl");
