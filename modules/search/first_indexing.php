<?php

/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */
CCanDo::checkAdmin();
$table   = CValue::get("table");
$mapping = CValue::get("mapping");
$error   = "";

if ($table) {
  $ds    = CSQLDataSource::get("std");
  $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CCompteRendu', `compte_rendu`.`compte_rendu_id`, 'create', '2014-05-01 16:04:00'
          FROM `compte_rendu`, `users_mediboard`, `functions_mediboard`
          WHERE `compte_rendu`.`object_id` IS NOT NULL
          AND `users_mediboard`.`user_id` =  `compte_rendu`.`author_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
  $ds->exec($query);
  CAppUI::displayAjaxMsg("l'opération en base s'est déroulée avec succès ", UI_MSG_OK);
}


try {
  if ($mapping) {
    $client_index = new CSearch();
    //create a client
    $client_index->createClient();
    $index = $client_index->createIndex(null, null, false);
    /** @var Elastica\Type $elasticaType */
    $type  = $client_index->createType($index, 'CCompteRendu');
    $array = array(
      "id"          => array('type' => 'integer', 'include_in_all' => true),
      "author_id"   => array('type' => 'integer', 'include_in_all' => true),
      "title"       => array('type' => 'string', 'include_in_all' => false),
      "body"        => array('type' => 'string', 'include_in_all' => true),
      "date"        => array('type'           => 'date',
                             'format'         => 'yyyy/MM/dd HH:mm:ss||yyyy/MM/dd',
                             'include_in_all' => true),
      "patient_id"  => array('type' => 'integer', 'include_in_all' => true),
      "function_id" => array('type' => 'integer', 'include_in_all' => true),
      "group_id"    => array('type' => 'integer', 'include_in_all' => true)
    );

    $client_index->createMapping($type, $array);
    CAppUI::displayAjaxMsg("L'index " . CAppUI::conf("db std dbname") . " s'est correctement créé", UI_MSG_OK);
  }
}
catch (Exception $e) {
  CAppUI::displayAjaxMsg("L'index " . CAppUI::conf("db std dbname") . " existe déjà", UI_MSG_ERROR);
  $error = "mapping";
}

$smarty = new CSmartyDP();
$smarty->assign("error", $error);
$smarty->display("inc_configure_serveur.tpl");

