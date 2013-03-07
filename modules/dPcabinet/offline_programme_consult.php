<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

// Initialisation des variables
$chir_id     = CValue::get("chir_id");
$function_id = CValue::get("function_id");
$date        = CValue::get("date", CMbDT::date());
$nb_months   = CValue::get("nb_months", 3);
$period      = CValue::get("period", CAppUI::pref("DefaultPeriod"));

// Récupération des plages de consultation disponibles
$plage = new CPlageconsult();
$listPlage = array();
$where = array();

// Praticiens sélectionnés
$praticien = new CMediusers;
if (CAppUI::pref("pratOnlyForConsult", 1)) {
  $listPrat = $praticien->loadPraticiens(PERM_EDIT, $function_id);
}
else {
  $listPrat = $praticien->loadProfessionnelDeSante(PERM_EDIT, $function_id);
}

$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat), $chir_id);

$order = "date, debut";

// Chargement des plages par date
$maxDate = CMbDT::date("-1 DAYS", $date);

for ($i = 1; $i <= $nb_months; $i++) {
  $minDate = CMbDT::date("+1 DAYS", $maxDate);
  $maxDate = CMbDT::transform("+1 MONTH", $minDate, "%Y-%m-01");
  $maxDate = CMbDT::date("-1 DAYS", $maxDate);
  $where["date"] = $ds->prepare("BETWEEN %1 AND %2", $minDate, $maxDate);
  $listPlages[CMbDT::transform(null, $minDate, "%B %Y")] = $plage->loadList($where, $order);
}

$bank_holidays = array_merge(CMbDT::bankHolidays($date), CMbDT::bankHolidays($maxDate));

// Chargement des places disponibles pour chaque plage
foreach ($listPlages as &$curr_month) {
  foreach ($curr_month as &$curr_plage) {
    $curr_plage->_ref_chir =& $listPrat[$curr_plage->chir_id];
    $curr_plage->loadRefs(false);
    $curr_plage->_ref_chir->loadRefFunction();
    $curr_plage->_listPlace = array();
    for ($i = 0; $i < $curr_plage->_total; $i++) {
      $minutes = $curr_plage->_freq * $i;
      $curr_plage->_listPlace[$i]["time"] = CMbDT::time("+ $minutes minutes", $curr_plage->debut);
      $curr_plage->_listPlace[$i]["consultations"] = array();
    }
    foreach ($curr_plage->_ref_consultations as &$consultation) {
      $consultation->loadRefPatient();
      // Chargement de la categorie
      $consultation->loadRefCategorie();
      $keyPlace = CMbDT::timeCountIntervals($curr_plage->debut, $consultation->heure, $curr_plage->freq);
      for ($i = 0;  $i < $consultation->duree; $i++) {
        if (isset($curr_plage->_listPlace[($keyPlace + $i)])) {
          $curr_plage->_listPlace[($keyPlace + $i)]["consultations"][] =& $consultation;
        }
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("print_date"     , CMbDT::dateTime());
$smarty->assign("chir_id"        , $chir_id);
$smarty->assign("plageconsult_id", null);
$smarty->assign("listPlages"     , $listPlages);
$smarty->assign("online"         , false);
$smarty->assign("bank_holidays"  , $bank_holidays);

$smarty->display("offline_programme_consult.tpl");
