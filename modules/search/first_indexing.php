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
$table       = CValue::get("table");
$mapping     = CValue::get("mapping");
$names_types = json_decode(stripslashes(CValue::get("names_types")));
$error       = "";

if ($table) {
  $ds    = CSQLDataSource::get("std");
  $date = CMbDT::dateTime();
  foreach ($names_types as $name_type) {
    switch ($name_type) {
      case 'CCompteRendu':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CCompteRendu', `compte_rendu`.`compte_rendu_id`, 'create', '$date'
          FROM `compte_rendu`, `users_mediboard`, `functions_mediboard`
          WHERE `compte_rendu`.`object_id` IS NOT NULL
          AND `users_mediboard`.`user_id` =  `compte_rendu`.`author_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      case 'CTransmissionMedicale':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CTransmissionMedicale', `transmission_medicale`.`transmission_medicale_id`, 'create', '$date'
          FROM `transmission_medicale`, `users_mediboard`, `functions_mediboard`
          WHERE `users_mediboard`.`user_id` =  `transmission_medicale`.`user_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      case 'CObservationMedicale':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CObservationMedicale', `observation_medicale`.`observation_medicale_id`, 'create', '$date'
          FROM `observation_medicale`, `users_mediboard`, `functions_mediboard`
          WHERE `users_mediboard`.`user_id` =  `observation_medicale`.`user_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      case 'CConsultation':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CConsultation', `consultation`.`consultation_id`, 'create', '$date'
          FROM `consultation`, `users_mediboard`, `functions_mediboard`, `plageconsult`
          WHERE `consultation`.`sejour_id` IS NOT NULL
          AND `plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`
          AND `users_mediboard`.`user_id` =  `plageconsult`.`chir_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      case 'CConsultAnesth':
        $query = "INSERT INTO `search_indexing` (`object_class`, `object_id`, `type`, `date`)
          SELECT 'CConsultAnesth', `consultation_anesth`.`consultation_anesth_id`, 'create', '$date'
          FROM `consultation_anesth`, `users_mediboard`, `functions_mediboard`, `plageconsult`, `consultation`
          WHERE `consultation_anesth`.`sejour_id` IS NOT NULL
          AND   `consultation_anesth`.`consultation_id` = `consultation`.`consultation_id`
          AND `plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`
          AND `users_mediboard`.`user_id` =  `plageconsult`.`chir_id`
          AND `functions_mediboard`.`function_id` = `users_mediboard`.`function_id`";
        break;
      default: $query ="";
    }
    $ds->exec($query);
  }
  CAppUI::displayAjaxMsg("l'opération en base s'est déroulée avec succès ", UI_MSG_OK);
}

try {
  if ($mapping) {
    $client_index = new CSearch();
    //create a client
    $client_index->createClient();
    $index = $client_index->getIndex(CAppUI::conf("db std dbname"));
    $client_index->firstIndexingMapping($names_types, $index);
  }
    CAppUI::displayAjaxMsg("L'index " . CAppUI::conf("db std dbname") . " s'est correctement créé", UI_MSG_OK);
}
catch (Exception $e) {
  CAppUI::displayAjaxMsg("L'index " . CAppUI::conf("db std dbname") . " existe déjà", UI_MSG_ERROR);
  $error = "mapping";
}

$smarty = new CSmartyDP();
$smarty->assign("error", $error);
$smarty->display("inc_configure_serveur.tpl");

