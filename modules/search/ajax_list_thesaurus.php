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

$entry = new CSearchThesaurusEntry();
$where ["user_id"] = " = $user->_id";
$step = 10;
$limit = "$start , $step";
$entry->user_id =  "$user->_id";
$thesaurus = $entry->loadList($where, null, $limit);
$nbThesaurus = $entry->countList($where);
foreach ($thesaurus as $_thesaurus) {
  /** @var $_thesaurus  CSearchThesaurusEntry*/
  $_thesaurus->loadRefsTargets();
}

$types = array();
$group = CGroups::loadCurrent();
if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
  $types = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
}

$smarty = new CSmartyDP();
$smarty->assign("thesaurus", $thesaurus);
$smarty->assign("start_thesaurus", $start);
$smarty->assign("entry", $entry);
$smarty->assign("types", $types);
$smarty->assign("user", $user);
$smarty->assign("nbThesaurus", $nbThesaurus);
$smarty->display("inc_search_thesaurus_entry.tpl");