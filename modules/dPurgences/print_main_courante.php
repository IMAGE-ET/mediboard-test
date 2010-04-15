<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $g;

$date = CValue::getOrSession("date", mbDate());
$offline = CValue::get("offline");
$date_before = mbDate("-2 DAY", $date);

// Chargement des rpu de la main courante
$sejour = new CSejour;

$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$where = array();

if ($offline){
	$where[] = "sejour.entree_reelle LIKE '$date%' OR (
    sejour.sortie_reelle IS NULL AND sejour.entree_reelle LIKE '$date_before%'
  )"; 
} else {
  $where["sejour.entree_reelle"] = "LIKE '$date%'";
}

$where[] = "sejour.type = 'urg' OR rpu.sejour_id";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$order = "sejour.entree_reelle ASC";

$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

$stats = array(
  "less_than_1" => 0,
  "more_than_75" => 0,
  "transferts_count" => 0, 
  "mutations_count" => 0,
  "etablissements_transfert" => array(),
  "services_mutation" => array(),
);

$csteByTime = array();
// Détail du chargement
foreach ($sejours as &$_sejour) {
  $_sejour->loadRefsFwd(1);
  $_sejour->loadRefRPU();  

  // Statistiques de mutations de sejours
  $service_mutation = $_sejour->_ref_service_mutation;
  if ($service_mutation->_id) {
    $stats["mutations_count"]++;
    $stat_service =& $stats["services_mutation"][$service_mutation->_id];
    if (!isset($stat_service)) {
      $stat_service = array(
        "ref" => $service_mutation,
        "count" => 0
      );
    }
    $stat_service["count"]++;
  }

  // Statistiques de transferts de sejours
  $etablissement_tranfert = $_sejour->_ref_etabExterne;
  if ($etablissement_tranfert->_id) {
    $stats["transferts_count"]++;
		$stat_etablissement =& $stats["etablissements_transfert"][$etablissement_tranfert->_id];
		if (!isset($stat_etablissement)) {
			$stat_etablissement = array(
        "ref" => $etablissement_tranfert,
				"count" => 0
			);
		}
  	$stat_etablissement["count"]++;
  }

  // Statistiques  d'âge de patient
  $patient =& $_sejour->_ref_patient;
  if ($patient->_age < "1") {
  	$stats["less_than_1"]++;
  }
	
  if ($patient->_age >= "75") {
    $stats["more_than_75"]++;
  }
  

	// Chargement nécessaire du mode offline
	if ($offline) {
		$_sejour->loadRefsConsultations();
		$_sejour->loadListConstantesMedicales();
		
		$patient =& $_sejour->_ref_patient;
		$patient->loadIPP();
		$patient->loadRefDossierMedical();
		
		$dossier_medical =& $patient->_ref_dossier_medical;
		$dossier_medical->countAntecedents();
		$dossier_medical->loadRefPrescription();
		$dossier_medical->loadRefsTraitements();
		
		$consult =& $_sejour->_ref_consult_atu;
		$consult->loadRefPatient(1);
		$consult->loadRefPraticien(1);
		$consult->loadRefsBack();
		$consult->loadRefsDocs();
		foreach ($consult->_ref_actes_ccam as $_ccam) {
		  $_ccam->loadRefExecutant();
		}
		
		$csteByTime = array();
		foreach ($_sejour->_list_constantes_medicales as $_constante_medicale) {
		  $csteByTime[$sejour->_id][$_constante_medicale->datetime] = array();
		  foreach (CConstantesMedicales::$list_constantes as $_constante => $_params) {
		    $csteByTime[$sejour->_id][$_constante_medicale->datetime][$_constante] = $_constante_medicale->$_constante;
		  }
		}
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date",$date);
$smarty->assign("stats", $stats);
$smarty->assign("sejours", $sejours);
$smarty->assign("csteByTime", $csteByTime);
$smarty->assign("offline", $offline);
$smarty->assign("dateTime", mbDateTime());
$smarty->display("print_main_courante.tpl");

?>