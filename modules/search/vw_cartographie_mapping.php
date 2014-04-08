<?php 

/**
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */


CCanDo::checkAdmin();
try{
  $client_index   = new CSearch();
  $client_index->createClient();
  $index          = $client_index->loadIndex();
  $mapping        = $index->getMapping();
} catch (Exception $e) {
  CAppUI::displayAjaxMsg("Le serveur de recherche n'est pas connecté", UI_MSG_ERROR);
  $mapping="";
}


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("mapping", $mapping);
$smarty->assign("mappingjson", json_encode($mapping));
$smarty->display("vw_cartographie_mapping.tpl");