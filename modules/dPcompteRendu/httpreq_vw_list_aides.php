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
$order_col_aide = CValue::getOrSession("order_col_aide");
$order_way      = CValue::getOrSession("order_way");
$aide_id        = CValue::getOrSession("aide_id", "0");

$order_by = $order_col_aide ? $order_col_aide . " " . $order_way : null;

$userSel = CMediusers::get($filter_user_id);
$userSel->loadRefFunction()->loadRefGroup();

$where = array();
if ($filter_class) {
  $where["class"] = "= '$filter_class'";
}

// Liste des aides pour le praticien

// Accès aux aides à la saisie de la fonction et de l'établissement
$module = CModule::getActive("dPcompteRendu");
$is_admin = $module && $module->canAdmin();
$access_function = $is_admin || CAppUI::conf("compteRendu CAideSaisie access_function");
$access_group    = $is_admin || CAppUI::conf("compteRendu CAideSaisie access_group");

$aides      = array();
$aidesCount = array();

$aides["user"]      = array();
$aidesCount["user"] = 0;
if ($access_function) {
  $aides["func"]      = array();
  $aidesCount["func"] = 0;
}
if ($access_group) {
  $aides["etab"]      = array();
  $aidesCount["etab"] = 0;
}

$_aide = new CAideSaisie();

$where["user_id"]   = "= '$userSel->user_id'";
$aides["user_ids"]  = array_keys($_aide->seek($keywords, $where, 1000));
$aides["user"]      = $_aide->seek($keywords, $where, $start["user"].", 30", true, null, $order_by);
$aidesCount["user"] = $_aide->_totalSeek;

CMbObject::massCountBackRefs($aides['user'], 'hypertext_links');
if (isset($aides['func'])) {
  CMbObject::massCountBackRefs($aides['func'], 'hypertext_links');
}
if (isset($aides['etab'])) {
  CMbObject::massCountBackRefs($aides['etab'], 'hypertext_links');
}

/** @var CAideSaisie $aide */
foreach ($aides["user"] as $aide) {
  $aide->loadRefUser();
  $aide->loadBackRefs('hypertext_links');
}
unset($where["user_id"]);
if (isset($aides["func"])) {
  $where["function_id"] = "= '$userSel->function_id'";
  $aides["func_ids"]    = array_keys($_aide->seek($keywords, $where, 1000));
  $aides["func"]        = $_aide->seek($keywords, $where, $start["func"].", 30", true, null, $order_by);
  $aidesCount["func"]   = $_aide->_totalSeek;
  foreach ($aides["func"] as $aide) {
    $aide->loadRefFunction();
    $aide->loadBackRefs('hypertext_links');
  }
  unset($where["function_id"]);
}

if (isset($aides["etab"])) {
  $where["group_id"]  = "= '{$userSel->_ref_function->group_id}'";
  $aides["etab_ids"]  = array_keys($_aide->seek($keywords, $where, 1000));
  $aides["etab"]      = $_aide->seek($keywords, $where, $start["etab"].", 30", true, null, $order_by);
  $aidesCount["etab"] = $_aide->_totalSeek;
  foreach ($aides["etab"] as $aide) {
    $aide->loadRefGroup();
    $aide->loadBackRefs('hypertext_links');
  }
  unset($where["group_id"]);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("userSel"       , $userSel);
$smarty->assign("aides"         , $aides);
$smarty->assign("aidesCount"    , $aidesCount);
$smarty->assign("filter_class"  , $filter_class);
$smarty->assign("start"         , $start);
$smarty->assign("order_col_aide", $order_col_aide);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("aide_id"       , $aide_id);

$smarty->display("inc_tabs_aides.tpl");
