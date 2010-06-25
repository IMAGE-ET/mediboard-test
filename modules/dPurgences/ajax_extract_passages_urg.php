<?php /* $Id: print_main_courante.php 8995 2010-05-25 08:56:25Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8995 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

ini_set("memory_limit", "512M");

$debut_selection = CValue::get("debut_selection");
$fin_selection   = CValue::get("fin_selection");

if (!$debut_selection || !$fin_selection) {
  $fin_selection   = mbDateTime();
  $debut_selection = mbDateTime("-7 DAY", $fin_selection);
}

// Chargement des rpu de la main courante
$sejour = new CSejour;

$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";

$where[] = "sejour.entree_reelle BETWEEN '$debut_selection' AND '$fin_selection' 
  OR (sejour.sortie_reelle IS NULL AND sejour.entree_reelle BETWEEN '$debut_selection' AND '$fin_selection')";

// RPUs
$where[] = "rpu.rpu_id IS NOT NULL";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$order = "sejour.entree_reelle ASC";

$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

$stats = array (
  "total" => 0,
  "less_than_1" => 0,
  "more_than_75" => 0,
  "transferts_count" => 0, 
  "mutations_count" => 0,
  "mutations_uhcd_count" => 0
);

// Détail du chargement
foreach ($sejours as &$_sejour) {
  $_sejour->loadRefsFwd(1);
  $_sejour->loadRefRPU();  
  $_sejour->_ref_rpu->loadRefSejourMutation();
  
  $stats["total"]++;

  // Statistiques de mutations de sejours
  $service_mutation = $_sejour->_ref_service_mutation;
  if ($service_mutation->_id) {
    $stats["mutations_count"]++;
    if ($service_mutation->uhcd) {
      $stats["mutations_uhcd_count"]++;
    }
  }

  // Statistiques de transferts de sejours
  $etablissement_tranfert = $_sejour->_ref_etabExterne;
  if ($etablissement_tranfert->_id) {
    $stats["transferts_count"]++;
  }
  
  // Statistiques  d'âge de patient
  $patient =& $_sejour->_ref_patient;
  if ($patient->_age < "1") {
    $stats["less_than_1"]++;
  }
    
  if ($patient->_age >= "75") {
    $stats["more_than_75"]++;
  }
}

$extractPassages = new CExtractPassages();
$extractPassages->date_extract    = mbDateTime();
$extractPassages->type            = "urg";
$extractPassages->debut_selection = $debut_selection;
$extractPassages->fin_selection   = $fin_selection;
$extractPassages->store();

$doc_valid = null;

// Appel de la fonction d'extraction du RPUSender
$rpuSender = $extractPassages->getRPUSender();
if (!$rpuSender) {
  CAppUI::stepAjax("Aucun sender définit dans le module dPurgences.", UI_MSG_ERROR);
}
$extractPassages = $rpuSender->extractURG($extractPassages, $stats);

CAppUI::stepAjax("Extraction de ".$stats['total']." RPUs du ".mbDateToLocale($debut_selection)." au ".mbDateToLocale($fin_selection)." terminée.", UI_MSG_OK);
if (!$extractPassages->message_valide)
  CAppUI::stepAjax("Le document produit n'est pas valide.", UI_MSG_WARNING);
else 
  CAppUI::stepAjax("Le document produit est valide.", UI_MSG_OK);

echo "<script type='text/javascript'>extract_passages_id = $extractPassages->_id;</script>";

?>