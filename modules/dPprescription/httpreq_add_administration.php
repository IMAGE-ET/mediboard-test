<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$line_id              = CValue::get("line_id");
$object_class         = CValue::get("object_class");
$quantite             = CValue::get("quantite");
$key_tab              = CValue::get("key_tab");
$dateTime             = CValue::get("dateTime");
$quantite             = is_numeric($quantite) ? $quantite : '';
$prise_id             = is_numeric($key_tab) ? $key_tab : '';
$unite_prise          = !is_numeric($key_tab) ? utf8_decode($key_tab) : '';
$date_sel             = CValue::get("date_sel");
$mode_plan            = CValue::get("mode_plan", false);
$prescription_id      = CValue::get("prescription_id");
$list_administrations = CValue::get("administrations");
$planification_id     = CValue::get("planification_id");
$mode_dossier         = CValue::get("mode_dossier");
$multiple_adm         = CValue::get("multiple_adm");

$administrations      = array();
$planification        = new CAdministration();
$selection            = array();
$constantes           = new CConstantesMedicales;
$latest_constantes    = array();
// Chargement de la ligne
$line = new $object_class;
$line->load($line_id);

if ($line instanceof CPrescriptionLineMedicament) {
  $line->_ref_produit->loadConditionnement();
}

// Chargement de la liste des administrations en fonction du token field fourni par le tpl
if($list_administrations){
  $_administrations = explode("|",$list_administrations);
  foreach($_administrations as $_administration_id){
    $administration = new CAdministration();
    $administration->load($_administration_id);
    $administration->loadRefsFwd();
    $administration->loadRefLog();
    $administration->loadRefsTransmissions();
    $administration->loadRefConstantesMedicales();
    if ($administration->_ref_constantes_medicales && $administration->_ref_constantes_medicales->_id) {
      $administration->_ref_constantes_medicales->loadRefUser();
    }
    $line =& $administration->_ref_object;
    $line->loadRefsFwd();
    if($line instanceof CPrescriptionLineMedicament){
      $line->_ref_produit->loadConditionnement();
			$line->loadRefProduitPrescription();
    }
    $administrations[$administration->_id] = $administration;
    $administration->_less_24h = ((strtotime(mbDateTime()) - strtotime($administration->dateTime)) / 3600) <= 24;
  }
}
// Recherche d'administration
else {
	$administration = new CAdministration();
	$administration->dateTime = $dateTime;
	$administration->prise_id = $prise_id;
	$administration->planification = 0;
	$administrations = $administration->loadMatchingList();
	foreach($administrations as $_administration){
	  $_administration->loadRefsFwd();
    $_administration->loadRefLog();
    $_administration->loadRefsTransmissions();
    $_administration->loadRefConstantesMedicales();
    if ($_administration->_ref_constantes_medicales && $_administration->_ref_constantes_medicales->_id) {
      $_administration->_ref_constantes_medicales->loadRefUser();
    }
    $line =& $_administration->_ref_object;
    $line->loadRefsFwd();
    if($line instanceof CPrescriptionLineMedicament){
      $line->_ref_produit->loadConditionnement();
      $line->loadRefProduitPrescription();
    }
	}
}

if($planification_id){
  $planification->load($planification_id);
  $planification->loadRefsFwd();
  $planification->loadRefLog();
  $line =& $planification->_ref_object;
  $line->loadRefsFwd();
  if($line instanceof CPrescriptionLineMedicament){
    $line->_ref_produit->loadConditionnement();
		$line->loadRefProduitPrescription();
  }
}

// Si une prise est specifiée (pas de moment unitaire), on charge la prise pour stocker l'unite de prise
if($prise_id){
	$prise = new CPrisePosologie();
	$prise->load($prise_id);
	$unite_prise = $prise->unite_prise;
}

$prise = new CPrisePosologie();
$prise->quantite = $quantite;

if($line instanceof CPrescriptionLineMedicament  && $line->_ref_produit->_ratio_UI){
  $prise->_quantite_UI = $prise->quantite / $line->_ref_produit->_ratio_UI;
}
	
// Chargement du sejour
$line->loadRefPrescription();
$line->_ref_prescription->loadRefObject();

$sejour = $line->_ref_prescription->_ref_object;
$sejour->loadRefPatient();
$patient =& $sejour->_ref_patient;
$patient->loadRefsAffectations();
$patient->_ref_curr_affectation->loadView();

// Heures disponibles pour l'administration
$hours = range(0,23);
foreach($hours as &$_hour){
  $_hour = str_pad($_hour, 2, "0", STR_PAD_LEFT);
}

$user_id = CAppUI::$user->_id;

// Transmission
$transmission = new CTransmissionMedicale();
$transmission->sejour_id = $sejour->_id;
$transmission->user_id = $user_id;

// Constantes médicales
if ($line instanceof CPrescriptionLineElement) {
  $element = $line->_ref_element_prescription;
  $element->loadBackRefs("constantes_items");
  
  foreach($element->_back["constantes_items"] as $_constante_item) {
    $selection[] = $_constante_item->field_constante;
  }
  
  $selection = array_flip($selection);
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, $selection);
  $constantes->patient_id = $sejour->_ref_patient->_id;
  $constantes->loadRefPatient();
  $constantes->context_id = $sejour->_id;
  $constantes->context_class = $sejour->_class;
  $constantes->loadRefContext();
  $latest_constantes = $patient->loadRefConstantesMedicales();
}

if ($line instanceof CPrescriptionLineMedicament && CAppUI::conf("pharmacie ask_stock_location_administration")) {
  $line->loadRefsProductsStocks();
}

$group = CGroups::loadCurrent();
$group->loadConfigValues();
$show_trash_24h = $group->_configs['dPprescription_CPrescription_show_trash_24h'];

$new_adm = new CAdministration();
$new_adm->_date = mbDate($dateTime);
$new_adm->_time = mbTime($dateTime);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("new_adm", $new_adm);
$smarty->assign("key_tab", $key_tab);
$smarty->assign("date_sel", $date_sel);
$smarty->assign("administrations", $administrations);
$smarty->assign("planification", $planification);
$smarty->assign("transmission", $transmission);
$smarty->assign("line", $line);
$smarty->assign("unite_prise", $unite_prise);
$smarty->assign("prise", $prise);
$smarty->assign("sejour", $sejour);
$smarty->assign("dateTime", $dateTime);
$smarty->assign("prise_id", $prise_id);
$smarty->assign("notToday", mbDate($dateTime) != mbDate());
$smarty->assign("mode_plan", $mode_plan);
$smarty->assign("hours", $hours);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("mode_dossier", $mode_dossier);
$smarty->assign("user_id", $user_id);
$smarty->assign("date", mbDate());
$smarty->assign("hour", mbTransformTime(null, mbTime(), "%H"));
$smarty->assign("selection", $selection);
$smarty->assign("constantes", $constantes);
$smarty->assign("latest_constantes", $latest_constantes);
$smarty->assign("params", CConstantesMedicales::$list_constantes);
$smarty->assign("is_praticien", CAppUI::$user->isPraticien());
$smarty->assign("show_trash_24h", $show_trash_24h);

$smarty->display("inc_vw_add_administration.tpl");
?>