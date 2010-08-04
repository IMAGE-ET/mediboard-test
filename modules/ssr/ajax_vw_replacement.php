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

$conge = new CPlageConge();
$conge->load($conge_id);

// Chargement d'un remplacement
$sejour->loadRefReplacement();
$replacement =& $sejour->_ref_replacement;
if ($replacement->_id) {
  $replacement->loadRefReplacer();
	$replacer =& $replacement->_ref_replacer;
	$replacer->loadRefFunction();
}

$sejour->loadRefPatient();
$patient =& $sejour->_ref_patient;

$patient->loadRefsSejours();
$sejours =& $patient->_ref_sejours;

// Chargement des praticiens
$user = new CMediusers();
$user->load($conge->user_id);
$user->loadRefFunction();
$users = $user->loadUsers(PERM_READ, $user->function_id);

$evenements_counts = array();

// Chargement
$evenement = new CEvenementSSR();
$where["sejour_id"    ] = CSQLDataSource::prepareIn(array_keys($sejours));
$where["therapeute_id"] = CSQLDataSource::prepareIn(array_keys($users));
foreach($evenement->loadList($where) as $_evenement) {
	@$evenements_counts[$_evenement->sejour_id][$_evenement->therapeute_id]++;
}

if (!$replacement->_id) {
  $replacement->conge_id = $conge_id;
  $replacement->sejour_id = $sejour_id;
}

// Chargement des evenements SSR dont le therapeute est le kine principal pendant sa periode de congé
if ($type == 'kine') {
	$sejour->loadRefBilanSSR();
	$bilan =& $sejour->_ref_bilan_ssr;
	
	$bilan->loadRefTechnicien();
	$kine_id = $bilan->_ref_technicien->kine_id;
	
  $date_debut = $conge->date_debut;
  $date_fin = mbDate("+1 DAY", $conge->date_fin);
	$evenement = new CEvenementSSR();
	$where = array();
	$where["therapeute_id"] = " = '$kine_id'";
	$where["sejour_id"] = " = '$sejour->_id'";
	$where["debut"] = "BETWEEN '$date_debut' AND '$date_fin'";
	$evenements = $evenement->loadList($where);
}

// Chargement des evenements SSR
if ($type == "reeducateur") {
	$evenement = new CEvenementSSR();
	$evenement->therapeute_id = $conge->user_id;
	$evenement->sejour_id = $sejour_id;
	$evenements = $evenement->loadMatchingList();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("evenements_counts", $evenements_counts);
$smarty->assign("sejours", $sejours);
$smarty->assign("users", $users);

$smarty->assign("sejour", $sejour);
$smarty->assign("replacement", $replacement);
$smarty->assign("conge", $conge);
$smarty->assign("user", $user);
$smarty->assign("evenements", $evenements);
$smarty->assign("type", $type);
$smarty->display("inc_vw_replacement.tpl");

?>