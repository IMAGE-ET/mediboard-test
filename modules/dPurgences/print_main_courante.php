<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $g;

$date = CValue::getOrSession("date");
$offline = CValue::get("offline");
$date_before = mbDate("-1 DAY", $date);

// Chargement des rpu de la main courante
$sejour = new CSejour;

$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";
$where = array();

if($offline){
	$where[] = "sejour.entree_reelle LIKE '$date%' OR (
    sejour.sortie_reelle IS NULL AND sejour.entree_reelle LIKE '$date_before%'
  )"; 
} else {
  $where["sejour.entree_reelle"] = "LIKE '$date%'";
}

$where["sejour.type"] = "= 'urg'";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$order = "sejour.entree_reelle ASC";

$listSejours = $sejour->loadList($where, $order, null, null, $ljoin);

$csteByTime = array();
foreach($listSejours as &$curr_sejour) {
  $curr_sejour->loadRefsFwd();
  $curr_sejour->loadRefRPU();  
	
	if($offline){
		$curr_sejour->loadRefsConsultations();
		$curr_sejour->loadListConstantesMedicales();
		
		$patient =& $curr_sejour->_ref_patient;
		$patient->loadIPP();
		$patient->loadRefDossierMedical();
		
		$dossier_medical =& $patient->_ref_dossier_medical;
		$dossier_medical->countAntecedents();
		$dossier_medical->loadRefPrescription();
		$dossier_medical->loadRefsTraitements();
		
		$consult =& $curr_sejour->_ref_consult_atu;
		$consult->loadRefPatient();
		$consult->loadRefPraticien();
		$consult->loadRefsBack();
		$consult->loadRefsDocs();
		foreach ($consult->_ref_actes_ccam as $_ccam) {
		  $_ccam->loadRefExecutant();
		}
		
		$csteByTime = array();
		foreach ($curr_sejour->_list_constantes_medicales as $_constante_medicale) {
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
$smarty->assign("listSejours", $listSejours);
$smarty->assign("csteByTime", $csteByTime);
$smarty->assign("offline", $offline);
$smarty->assign("dateTime", mbDateTime());
$smarty->display("print_main_courante.tpl");

?>