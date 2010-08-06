<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$date = CValue::getOrSession("date", mbDate());

$date_min = mbDate("last monday", mbDate("+1 day", $date));
$date_max = mbDate("+7 DAY", $date_min);

// Comptage des événements hors-séjours
$group = "sejour.sejour_id";

$query = new CRequest;
$query->addTable("evenement_ssr");
$query->addColumn("COUNT(evenement_ssr_id)", "evenements_count");
$query->addWhereClause("debut", "BETWEEN '$date_min' AND '$date_max'");
$query->addWhereClause("type", "= 'ssr'");

$query->addLJoinClause("sejour", "sejour.sejour_id = evenement_ssr.sejour_id");
$query->addColumn("sejour.sejour_id");
$query->addWhereClause(null, "debut NOT BETWEEN DATE(entree) AND DATE(ADDDATE(sortie, 1))");
$query->addWhereClause("sejour.annule", "!= '1'");
$query->addGroup("sejour.sejour_id");

$sejour = new CSejour;
$ds = $sejour->_spec->ds;
$evenements_counts = array();
foreach($ds->loadList($query->getRequest()) as $row) {
	$evenements_counts[$row["sejour_id"]] = $row["evenements_count"];
}

// Chargement des séjours concernés
$sejours = $sejour->loadAll(array_keys($evenements_counts));
$sejours_count = count($sejours);

// Détails sur les séjours concernés
foreach ($sejours as $_sejour){  
  $_sejour->checkDaysRelative($date);

  // Patient
  $_sejour->loadRefPatient();
  $patient =& $_sejour->_ref_patient;
  $patient->loadIPP();
}


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("sejours_count", $sejours_count);
$smarty->assign("evenements_counts", $evenements_counts);
$smarty->display("inc_evenements_hors_sejours.tpl");

?>