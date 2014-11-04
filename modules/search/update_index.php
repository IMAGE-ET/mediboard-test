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
$names_types = CValue::get("types");
$error       = "";

try {

    $client_index = new CSearch();
    //create a client
    $client_index->createClient();
    $index = $client_index->getIndex(CAppUI::conf("db std dbname"))->exists();
    $client_index->updateIndex($index);
    CAppUI::displayAjaxMsg("L'index " . CAppUI::conf("db std dbname") . " s'est correctement mis à jour", UI_MSG_OK);

}
catch (Exception $e) {
  CAppUI::displayAjaxMsg("L'index " . CAppUI::conf("db std dbname") . " n'a pas été mis à jour", UI_MSG_ERROR);
  $error = "update";
}

$smarty = new CSmartyDP();
$smarty->assign("error", $error);
$smarty->display("inc_configure_es.tpl");