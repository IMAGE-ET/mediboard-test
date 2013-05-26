<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */


CCanDo::checkRead();

// Plateaux disponibles
$show_cancelled_services = CValue::getOrSession("show_cancelled_services");
$technicien_id = CValue::get("technicien_id");
$service_id = CValue::getOrSession("service_id");
$date = CValue::getOrSession("date", CMbDT::date());

$technicien = new CTechnicien();
$technicien->load($technicien_id);
$technicien->loadRefKine();
$kine_id = $technicien->_ref_kine->_id;

$sejours = CBilanSSR::loadSejoursSSRfor($technicien_id, $date, $show_cancelled_services);
$services = array();

$all_sejours = array();
foreach ($sejours as $_sejour) {
  // Filtre sur service
  $service = $_sejour->loadFwdRef("service_id");
  $services[$service->_id] = $service;
  if (!$technicien_id && $service_id && $_sejour->service_id != $service_id) {
    unset($sejours[$_sejour->_id]);
    continue;
  }
  
  $all_sejours[] = $_sejour;
  $_sejour->checkDaysRelative($date);
  $_sejour->loadRefPatient(1);
  $_sejour->loadRefBilanSSR()->getDateEnCours($date);
}

// Blows id keys
array_multisort(CMbArray::pluck($sejours, "_ref_patient", "nom"), SORT_ASC, $sejours);

// Ajustements services
$service = new CService;
$service->load($service_id);
$services[$service->_id] = $service;
unset($services[""]);

// Remplacements
$replacement = new CReplacement;
$replacements = $replacement->loadListFor($kine_id, $date);

foreach ($replacements as $_replacement) {
  // Détails des séjours remplacés
  $_replacement->loadRefSejour();
  $sejour =& $_replacement->_ref_sejour;
  if ($sejour->sortie < $date) {
    unset($replacements[$_replacement->_id]);
    continue;
  }
  
  $all_sejours[] = $sejour;
  $sejour->checkDaysRelative($date);
  $sejour->loadRefPatient(1);
  $sejour->loadRefBilanSSR();

  // Détail sur le congé
  $_replacement->loadRefConge()->loadRefUser()->loadRefFunction();
}

// Chargement du séjours potentiellement remplacés
$technicien->loadRefCongeDate($date);
$conge = $technicien->_ref_conge_date;
if ($conge->_id) {
  foreach ($sejours as $_sejour) {
    $_sejour->loadRefReplacement($conge->_id);
  }
}

// Nombre de séjours
$sejours_count = count($sejours) + count($replacements);

// Couleurs
$colors = CColorLibelleSejour::loadAllFor(CMbArray::pluck($all_sejours, "libelle"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("show_cancelled_services", $show_cancelled_services);
$smarty->assign("technicien_id", $technicien_id);
$smarty->assign("service_id", $service_id);
$smarty->assign("colors", $colors);
$smarty->assign("sejours", $sejours);
$smarty->assign("sejours_count", $sejours_count);
$smarty->assign("services", $services);
$smarty->assign("replacements", $replacements);
$smarty->display("inc_sejours_technicien.tpl");
