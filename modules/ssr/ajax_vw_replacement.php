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

$sejour_id = CValue::getOrSession("sejour_id");
$conge_id = CValue::getOrSession("conge_id");
$type = CValue::getOrSession("type");

// Standard plage
$conge = new CPlageConge();
$conge->load($conge_id);

// Week dates
$date = CValue::getOrSession("date", CMbDT::date());
$monday = CMbDT::date("last monday", CMbDT::date("+1 DAY", $date));
$sunday = CMbDT::date("next sunday", CMbDT::date("-1 DAY", $date));

// Pseudo plage for user activity
if (preg_match("/[deb|fin][\W][\d]+/", $conge_id)) {
  list($activite, $user_id) = explode("-", $conge_id);
  $limit = $activite == "deb" ? $monday : $sunday;
  $conge = CPlageConge::makePseudoPlage($user_id, $activite, $limit);
}

// Séjour unique
if ($sejour_id) {
  // Chargement du séjour 
  $sejour = new CSejour();
  $sejour->load($sejour_id);
  $sejours[$sejour->_id] = $sejour;
  
  // Chargement d'un remplacement possible
  $replacement = $sejour->loadRefReplacement($conge_id);
  if ($replacement->_id) {
    $replacement->loadRefsNotes();
    $replacement->loadRefReplacer()->loadRefFunction();
  }
}
// Tous les séjours de la plage
else {
  // Chargement des séjours
  $sejours = CBilanSSR::loadSejoursSurConges($conge, $monday, $sunday);
  $patients = CMbObject::massLoadFwdRef($sejours, "patient_id");
  
  // Pas de remplacement pour une collection de séjours
  $replacement = new CReplacement();
}

// Chargement des praticiens
$user = new CMediusers();
$user->load($conge->user_id);
$user->loadRefFunction();
$users = $user->loadUsers(PERM_READ, $user->function_id);

// Séjours des patients
$therapeutes = array();
$all_sejours = array();
foreach ($sejours as $_sejour) {
  $patient = $_sejour->loadRefPatient();
  foreach ($patient->loadRefsSejours() as $_other_sejour) {
    $_other_sejour->loadRefPatient();
    $all_sejours[$_other_sejour->_id] = $_other_sejour;
    $_other_sejour->loadRefBilanSSR()->loadRefTechnicien();
    $therapeutes += CEvenementSSR::getAllTherapeutes($_other_sejour->patient_id, $user->function_id); 
  }
}

// Chargement des comptes d'événements
$evenements_counts = array();
$evenement = new CEvenementSSR();
$where["sejour_id"    ] = CSQLDataSource::prepareIn(array_keys($all_sejours));
$where["therapeute_id"] = CSQLDataSource::prepareIn(array_keys($therapeutes));
/** @var CEvenementSSR $_evenement */
foreach ($evenement->loadList($where) as $_evenement) {
  @$evenements_counts[$_evenement->sejour_id][$_evenement->therapeute_id]++;
}

if (!$replacement->_id) {
  $replacement->conge_id  = $conge_id;
  $replacement->sejour_id = $sejour_id;
}

$transfer_count = 0;
$transfer_counts = array();

// Transfer event count
if ($type == 'kine') {
  $date_min = $conge->date_debut;
  $date_max = CMbDT::date("+1 DAY", $conge->date_fin);
  foreach ($sejours as $_sejour) {
    $bilan = $_sejour->loadRefBilanSSR();
    $tech = $bilan->loadRefTechnicien();
    $where = array();
    $where["sejour_id"]     = " = '$_sejour->_id'";
    $where["therapeute_id"] = " = '$tech->kine_id'";
    $where["debut"] = "BETWEEN '$date_min' AND '$date_max'";
    $transfer_count += $evenement->countList($where);
  }
}

// Transfer event counts
if ($type == "reeducateur") {
  $date_min = max($monday, $conge->date_debut);
  $date_max = min($sunday, $conge->date_fin);
  $where = array();
  $where["sejour_id"]     = " = '$sejour->_id'";
  $where["therapeute_id"] = " = '$conge->user_id'";
  foreach (range(0, 6) as $weekday) {
    $day = CMbDT::date("+$weekday DAYS", $monday);
    if (!CMbRange::in($day, $date_min, $date_max)) {
      $transfer_counts[$day] = 0;
      continue;
    }
    $after = CMbDT::date("+1 DAY", $day);
    $where["debut"] = "BETWEEN '$day' AND '$after'";
    $count = $evenement->countList($where);
    $transfer_counts[$day] = $count;
    $transfer_count += $count;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("evenements_counts", $evenements_counts);
$smarty->assign("sejours", $sejours);
$smarty->assign("all_sejours", $all_sejours);
$smarty->assign("therapeutes", $therapeutes);
$smarty->assign("users", $users);
$smarty->assign("transfer_count", $transfer_count);
$smarty->assign("transfer_counts", $transfer_counts);
$smarty->assign("sejour", reset($sejours));
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("replacement", $replacement);
$smarty->assign("conge", $conge);
$smarty->assign("user", $user);
$smarty->assign("type", $type);
$smarty->display("inc_vw_replacement.tpl");
