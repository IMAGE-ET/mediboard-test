<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

$user  = CMediusers::get();
$user->loadRefFunction()->loadRefGroup();
$start         = (int)CValue::get("start_thesaurus", 0);
$choose         = CValue::get("_choose");

$entry = new CSearchThesaurusEntry();
$entry->getDS();

$where = array();
$types = array();
$group = CGroups::loadCurrent();

if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
  $types = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
}

$contextes         = CValue::get("contextes");
$contextes = ($contextes) ? $contextes : CSearchLog::loadContextes();
$where["contextes"] = $entry->_spec->ds->prepareIn($contextes);



if ($choose) {
  $select_choose = explode(" ", $choose);
  $chaine = " = $select_choose[1]";
  $data = $ds->prepare($chaine);
  switch ($select_choose[0]) {
    case "user_id":
      $where ["user_id"] = $data;
      break;

    case "function_id":
      $where ["function_id"] = $data;
      break;

    case "group_id":
      $where ["group_id"] = $data;
      break;

    default:
  }
}
else {
  $where ["user_id"] = " = $user->_id";
}

$step = 10;
$limit = "$start , $step";
$thesaurus = $entry->loadList($where, null, $limit);
$nbThesaurus = $entry->countList($where);
foreach ($thesaurus as $_thesaurus) {
  /** @var $_thesaurus  CSearchThesaurusEntry*/
  $_thesaurus->loadRefsTargets();
  foreach ($_thesaurus->_atc_targets as $_target) {
    foreach ($_target->_ref_target as $_atc) {
      $object = new CMedicamentClasseATC();
      $_target->_libelle = $object->getLibelle($_target->object_id);
    }
  }
}

$smarty = new CSmartyDP();
$smarty->assign("thesaurus", $thesaurus);
$smarty->assign("start_thesaurus", $start);
$smarty->assign("entry", $entry);
$smarty->assign("types", $types);
$smarty->assign("user", $user);
$smarty->assign("nbThesaurus", $nbThesaurus);
$smarty->display("inc_search_thesaurus_entry.tpl");