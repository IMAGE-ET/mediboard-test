<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$types = array();
$group = CGroups::loadCurrent();
if (CAppUI::conf("search active_handler active_handler_search_types", $group)) {
  $types = explode("|", CAppUI::conf("search active_handler active_handler_search_types", $group));
}
$log = new CSearchLog();
$contextes = $log->loadContextes();

$test_search = new CSearch();
$test_search->testConnection($group);

$smarty = new CSmartyDP();
$smarty->assign("types", $types);
$smarty->assign("contextes", $contextes);
$smarty->display("vw_search_log.tpl");