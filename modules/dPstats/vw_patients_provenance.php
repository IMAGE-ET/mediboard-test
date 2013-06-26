<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Stats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19288 $
 */

CCanDo::read();

$year = CValue::get("year", CMbDT::transform(null, null, "%Y"));
$type = CValue::get("type", "traitant");

// Compteur d'années
$years = array();
for ($_year = 1980; $_year <= 2030; $_year++) {
  $years[] = $_year;
}

// En utilisant les médecins adressant le séjour
$queryAdresse = "SELECT
                   COUNT(DISTINCT(`sejour`.`sejour_id`)) AS total,
                   `medecin`.`nom`, `medecin`.`prenom`, `medecin`.`adresse`, `medecin`.`cp`
                 FROM `sejour`
                 LEFT JOIN `medecin`
                   ON `medecin`.`medecin_id` = `sejour`.`adresse_par_prat_id`
                 WHERE `entree` BETWEEN '$year-01-01' AND '$year-12-31'
                 GROUP BY `sejour`.`adresse_par_prat_id`
                 ORDER BY total DESC";

// En utilisant le médecin traitant
$queryTraitant = "SELECT
                    COUNT(DISTINCT(`sejour`.`sejour_id`)) AS total,
                    `medecin`.`nom`, `medecin`.`prenom`, `medecin`.`adresse`, `medecin`.`cp`
                  FROM `sejour`
                  LEFT JOIN `patients`
                    ON `patients`.`patient_id` = `sejour`.`patient_id`
                  LEFT JOIN `medecin`
                    ON `medecin`.`medecin_id` = `patients`.`medecin_traitant`
                  WHERE `entree` BETWEEN '$year-01-01' AND '$year-12-31'
                  GROUP BY `patients`.`medecin_traitant`
                  ORDER BY total DESC";
$source = CSQLDataSource::get("std");
$listResult = $source->loadList($type == "traitant" ? $queryTraitant : $queryAdresse);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("years"      , $years);
$smarty->assign("year"       , $year);
$smarty->assign("type"       , $type);
$smarty->assign("listResult" , $listResult);

$smarty->display("vw_patients_provenance.tpl");
