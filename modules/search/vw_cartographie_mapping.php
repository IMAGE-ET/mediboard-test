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

$ds = CSQLDataSource::get("std");
try{
  // récupération du client
  $client_index   = new CSearch();
  $client_index->createClient();

  // récupération de l'index, cluster, mapping
  $index      = $client_index->loadIndex();
  $name_index = $index->getName();
  $cluster    = $client_index->_client->getCluster();
  $mapping    = $index->getMapping();

  // récupération de la taille totale des indexes
  $size = $index->getStats()->get("_all");
  $size = CMbString::toDecaBinary($size["primaries"]["store"]["size_in_bytes"]);

  // récupération du nombre de docs "indexés" et "à indexer"
  $search = new CSearchIndexing();
  $nbdocs_indexed      = $index->count();
  $nbdocs_to_index = $search->countList();

  // récupération des types d'éléments restant à indexer.
  $nbdocs_to_index_by_type = $search->countMultipleList(null, null, "object_class", null, "`object_class`, COUNT(`object_class`) AS `total`");

  // récupération du statut de la connexion et du cluster
  $status     = $cluster->getHealth()->getStatus();
  $connexion  = $client_index->_client->hasConnection();

} catch (Exception $e) {
  CAppUI::displayAjaxMsg("Le serveur de recherche n'est pas connecté", UI_MSG_ERROR);
  // valeur par défaut des variables en cas d'erreur
  $mapping    = "";
  $nbdocs_indexed      = "";
  $nbdocs_to_index = "";
  $nbdocs_to_index_by_type = array();
  $status     = "";
  $connexion  = "0";
  $name_index = "";
  $size ="0";
}


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("mapping", $mapping);
$smarty->assign("mappingjson", json_encode($mapping));
$smarty->assign("nbDocs_indexed", $nbdocs_indexed);
$smarty->assign("nbdocs_to_index", $nbdocs_to_index);
$smarty->assign("nbdocs_to_index_by_type", $nbdocs_to_index_by_type);
$smarty->assign("status", $status);
$smarty->assign("name_index", $name_index);
$smarty->assign("connexion", $connexion);
$smarty->assign("size", $size);
$smarty->display("vw_cartographie_mapping.tpl");