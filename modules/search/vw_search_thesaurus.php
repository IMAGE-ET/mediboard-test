<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

$date  = CMbDT::date("-1 month");
$user  = CMediusers::get();
$user->loadRefFunction()->loadRefGroup();


$entry = new CSearchThesaurusEntry();
$entry->user_id =  "$user->_id";
$thesaurus = $entry->loadMatchingList();
foreach ($thesaurus as $_thesaurus) {
  /** @var $_thesaurus  CSearchThesaurusEntry*/
  $_thesaurus->loadRefsTargets();
}

$types = array();
$group = CGroups::loadCurrent();
if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
  $types = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
}

$log = new CSearchLog();
$log->testConnection($group);

$contextes = $log->loadContextes();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("start", 0);
$smarty->assign("results", array());
$smarty->assign("time", 0);
$smarty->assign("nbresult", 0);
$smarty->assign("thesaurus", $thesaurus);
$smarty->assign("entry", $entry);
$smarty->assign("types", $types);
$smarty->assign("user", $user);
$smarty->assign("contextes", $contextes);
$smarty->display("vw_search_thesaurus.tpl");