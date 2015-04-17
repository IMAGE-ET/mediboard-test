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
$group = CGroups::loadCurrent();
$test_search = new CSearch();
$test_search->testConnection($group);

try{
  // récupération du client
  $client_index   = new CSearch();
  $client_index->createClient();
  $infos_index = $client_index->loadCartoInfos();

} catch (Exception $e) {
  $infos_index = array();
  CAppUI::displayAjaxMsg("Le serveur de recherche n'est pas connecté", UI_MSG_WARNING);

}

try{
  // récupération des données pour les journaux utilisateurs
  $client_log   = new CSearchLog();
  $client_log->createClient();
  $infos_log = $client_log->loadCartoInfos();

} catch (Exception $e) {
  $infos_log = array();
  CAppUI::displayAjaxMsg("Le serveur de recherche de journaux n'est pas connecté", UI_MSG_WARNING);

}

try {
  // récupération des données pour les journaux utilisateurs
  $wrapper   = new CSearchFileWrapper(null, null);
  $infos_tika = $wrapper->loadTikaInfos();

} catch (Exception $e) {
  mbLog($e);
  $infos_tika = "";
  CAppUI::displayAjaxMsg("Le serveur d'extraction de fichiers n'est pas connecté", UI_MSG_WARNING);
}

$ds = CSQLDataSource::get("std");
$query = "SELECT MIN(`date`) as oldest_datetime from `search_indexing`";
$result = $ds->exec($query);
$oldest_datetime = $ds->fetchObject($result)->oldest_datetime;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("infos_index", $infos_index);
$smarty->assign("infos_log", $infos_log);
$smarty->assign("infos_tika", $infos_tika);
$smarty->assign("oldest_datetime", $oldest_datetime);

$smarty->display("vw_cartographie_mapping.tpl");