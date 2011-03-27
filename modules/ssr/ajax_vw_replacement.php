<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$sejour_id = CValue::getOrSession("sejour_id");
$conge_id = CValue::getOrSession("conge_id");
$type = CValue::getOrSession("type");
$sejour = new CSejour();
$sejour->load($sejour_id);

// Standard plage
$conge = new CPlageConge();
$conge->load($conge_id);

// Week dates
$date = CValue::getOrSession("date", mbDate());
$monday = mbDate("last monday", mbDate("+1 DAY", $date));
$sunday = mbDate("next sunday", mbDate("-1 DAY", $date));

// Pseudo plage for user activity
if (preg_match("/[deb|fin][\W][\d]+/", $conge_id)) {
  list($activite, $user_id) = explode("-", $conge_id);
  $limit = $activite == "deb" ? $monday : $sunday;
  $conge = CPlageConge::makePseudoPlage($user_id, $activite, $limit);
}

// Chargement d'un remplacement
$replacement = $sejour->loadRefReplacement($conge_id);
if ($replacement->_id) {
  $replacement->loadRefsNotes();
	$replacement->loadRefReplacer()->loadRefFunction();
}

// Séjours du patient
$patient = $sejour->loadRefPatient();
$sejours = $patient->loadRefsSejours();
foreach ($sejours as $_sejour) {
	$_sejour->loadRefBilanSSR()->loadRefTechnicien();
}

// Chargement des praticiens
$user = new CMediusers();
$user->load($conge->user_id);
$user->loadRefFunction();
$users = $user->loadUsers(PERM_READ, $user->function_id);

$evenements_counts = array();

$therapeutes = CEvenementSSR::getAllTherapeutes($sejour->patient_id, $user->function_id);

// Chargement
$evenement = new CEvenementSSR();
$where["sejour_id"    ] = CSQLDataSource::prepareIn(array_keys($sejours));
$where["therapeute_id"] = CSQLDataSource::prepareIn(array_keys($therapeutes));
foreach ($evenement->loadList($where) as $_evenement) {
	@$evenements_counts[$_evenement->sejour_id][$_evenement->therapeute_id]++;
}

if (!$replacement->_id) {
  $replacement->conge_id = $conge_id;
  $replacement->sejour_id = $sejour_id;
}

// Transfer event count
if ($type == 'kine') {
	$bilan = $sejour->loadRefBilanSSR();
	$kine = $bilan->loadRefTechnicien();
  $date_min = $conge->date_debut;
  $date_max = mbDate("+1 DAY", $conge->date_fin);

	$where = array();
  $where["sejour_id"]     = " = '$sejour->_id'";
	$where["therapeute_id"] = " = '$kine->_id'";
	$where["debut"] = "BETWEEN '$date_min' AND '$date_max'";
	$transfer_count = $evenement->loadList($where);
}

// Transfer event counts
if ($type == "reeducateur") {
  $date_min = max($monday, $conge->date_debut);
  $date_max = min($sunday, $conge->date_fin);
	$where = array();
  $where["sejour_id"]     = " = '$sejour->_id'";
  $where["therapeute_id"] = " = '$conge->user_id'";
  $transfer_count = 0;
  foreach(range(0,6) as $weekday) {
  	$day = mbDate("+$weekday DAYS", $monday);
  	if (!CMbRange::in($day, $date_min, $date_max)) {
      $transfer_counts[$day] = 0;
      continue;
  	}
  	$after = mbDate("+1 DAY", $day);
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
$smarty->assign("therapeutes", $therapeutes);
$smarty->assign("users", $users);
$smarty->assign("transfer_count", $transfer_count);
$smarty->assign("transfer_counts", $transfer_counts);

$smarty->assign("sejour", $sejour);
$smarty->assign("replacement", $replacement);
$smarty->assign("conge", $conge);
$smarty->assign("user", $user);
$smarty->assign("type", $type);
$smarty->display("inc_vw_replacement.tpl");

?>