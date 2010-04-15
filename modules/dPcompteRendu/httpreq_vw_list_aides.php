<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;
$can->needsRead();

// Utilisateur sélectionné ou utilisateur courant
$filter_user_id = CValue::getOrSession("filter_user_id");
$filter_class   = CValue::getOrSession("filter_class");
$start          = CValue::getOrSession("start");
$keywords       = CValue::getOrSession("keywords");

$userSel = new CMediusers;
$userSel->load($filter_user_id ? $filter_user_id : $AppUI->user_id);
$userSel->loadRefs();
$userSel->_ref_function->loadRefGroup();

if ($userSel->isPraticien()) {
  CValue::setSession("filter_user_id", $userSel->user_id);
  $filter_user_id = $userSel->user_id;
}

$where = array();
if ($filter_class) {
  $where["class"] = "= '$filter_class'";
}

$order = array("group_id", "function_id", "user_id", "class", "depend_value_1", "field", "name");

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
  $userSel->loadRefFunction();
  $_aide = new CAideSaisie();
  
  $where["user_id"] = "= '$userSel->user_id'";
  $aides["user"] = $_aide->seek($keywords, $where, $start["user"].", 30", true);
  $aidesCount["user"] = $_aide->_totalSeek;
  foreach($aides["user"] as $aide) {
    $aide->loadRefsFwd();
  }
  unset($where["user_id"]);

  $where["function_id"] = "= '$userSel->function_id'";
  $aides["func"] = $_aide->seek($keywords, $where, $start["func"].", 30", true);
  $aidesCount["func"] = $_aide->_totalSeek;
  foreach($aides["func"] as $aide) {
    $aide->loadRefsFwd();
  }
  unset($where["function_id"]);

  $where["group_id"] = "= '{$userSel->_ref_function->group_id}'";
  $aides["etab"] = $_aide->seek($keywords, $where, $start["etab"].", 30", true);
  $aidesCount["etab"] = $_aide->_totalSeek;
  foreach($aides["etab"] as $aide) {
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

$smarty->display("inc_tabs_aides.tpl");
