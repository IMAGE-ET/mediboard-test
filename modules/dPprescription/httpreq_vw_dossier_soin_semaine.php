<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$date = CValue::get("date");
$prescription_id = CValue::get("prescription_id");

// Initialisations
$sejour = new CSejour();
$patient = new CPatient();

// Chargement des dates de debut de semaine et de fin de semaine
$monday = mbDate("last monday", $date);
$sunday = mbDate("+ 7 DAYS", $monday);

$next_week = mbDate("+1 week", $date);
$prev_week = mbDate("-1 week", $date);
     
$dates = array();		 
for ($i = 0; $i < 7; $i++) {
  $_day = mbDate("+$i day", $monday);
  $dates[] = $_day;
}

// Chargement de la prescription
$prescription = new CPrescription();
if($prescription_id){
  $prescription->load($prescription_id);
  $prescription->loadRefsLinesMed("1","1");
	foreach($prescription->_ref_prescription_lines as $_line_med){
		$_line_med->loadRefProduitPrescription();
		$_line_med->loadRefLogSignee();
	}
  $prescription->loadRefsLinesElementByCat("1","","service");
  $prescription->loadRefsPrescriptionLineMixes("","1");
  foreach($prescription->_ref_prescription_line_mixes as &$_line_mix){
    $_line_mix->loadRefsLines();
    $_line_mix->loadRefPraticien();
		$_line_mix->loadRefLogSignaturePrat();
  }

  // Chargement du poids et de la chambre du patient
  $sejour =& $prescription->_ref_object;
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $patient =& $sejour->_ref_patient;
  $patient->loadRefConstantesMedicales();

  // Calcul du plan de soin sur les 5 jours
  $prescription->calculPlanSoin($dates, 0, 1);
}

// Calcul du nombre de produits (rowspan)
$prescription->calculNbProduit();

// Chargement des transmissions qui ciblent les lignes de la prescription
$prescription->loadAllTransmissions();

// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour"       , $sejour);
$smarty->assign("patient"      , $patient);
$smarty->assign("categories"   , $categories);
$smarty->assign("dates"        , $dates);
$smarty->assign("prescription" , $prescription);
$smarty->assign("now"          , mbDate());
$smarty->assign("categorie"    , new CCategoryPrescription());
$smarty->assign("monday"       , $monday);
$smarty->assign("sunday"       , $sunday);
$smarty->assign("prev_week"    , $prev_week);
$smarty->assign("next_week"    , $next_week);
$smarty->assign("params"       , CConstantesMedicales::$list_constantes);

$smarty->display("../../dPprescription/templates/inc_vw_dossier_soin_semaine.tpl");

?>