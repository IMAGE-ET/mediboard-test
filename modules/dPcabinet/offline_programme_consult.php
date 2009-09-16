<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */



global $AppUI, $can;

$can->needsRead();
$ds = CSQLDataSource::get("std");

// Initialisation des variables
$chir_id   = mbGetValueFromGet("chir_id");
$date      = mbGetValueFromGet("date", mbDate());
$nb_months = mbGetValueFromGet("nb_months", 3);
$period    = mbGetValueFromGet("period", $AppUI->user_prefs["DefaultPeriod"]);

// Récupération des plages de consultation disponibles
$plage = new CPlageconsult;
$listPlage = array();
$where = array();

// Praticiens sélectionnés
$praticien = new CMediusers;
$listPrat = $praticien->loadPraticiens(PERM_EDIT);

$where["chir_id"] = $ds->prepareIn(array_keys($listPrat), $chir_id);

$order = "date, debut";

// Chargement des plages par date
$maxDate = mbDate("-1 DAYS", $date);

for($i = 1; $i <= $nb_months; $i++) {
  $minDate = mbDate("+1 DAYS", $maxDate);
  $maxDate = mbTransformTime("+1 MONTH", $minDate, "%Y-%m-01");
  $maxDate = mbDate("-1 DAYS", $maxDate);
  $where["date"] = $ds->prepare("BETWEEN %1 AND %2", $minDate, $maxDate);
  $listPlages[mbTransformTime(null, $minDate, "%B %Y")] = $plage->loadList($where, $order);
}

// Chargement des places disponibles pour chaque plage
foreach($listPlages as &$curr_month) {
  foreach ($curr_month as &$curr_plage) {
    $curr_plage->_ref_chir =& $listPrat[$curr_plage->chir_id];
    $curr_plage->loadRefs(false);
		$curr_plage->_ref_chir->loadRefFunction();
    $curr_plage->_listPlaces = array();
    for ($i = 0; $i < $curr_plage->_total; $i++) {
      $minutes = $curr_plage->_freq * $i;
      $curr_plage->_listPlace[$i]["time"] = mbTime("+ $minutes minutes", $curr_plage->debut);
      $curr_plage->_listPlace[$i]["consultations"] = array();
    }
    foreach ($curr_plage->_ref_consultations as &$consultation) {
      $consultation->loadRefPatient();
      // Chargement de la categorie
      $consultation->loadRefCategorie();
      $keyPlace = mbTimeCountIntervals($curr_plage->debut, $consultation->heure, $curr_plage->freq);
      for  ($i = 0;  $i < $consultation->duree; $i++) {
        if (isset($curr_plage->_listPlace[($keyPlace + $i)])) {
          $curr_plage->_listPlace[($keyPlace + $i)]["consultations"][] =& $consultation;
        }
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("print_date"     , mbDateTime());
$smarty->assign("chir_id"        , $chir_id);
$smarty->assign("plageconsult_id", null);
$smarty->assign("listPlages"     , $listPlages);
$smarty->assign("online"         , false);

$smarty->display("offline_programme_consult.tpl");

?>