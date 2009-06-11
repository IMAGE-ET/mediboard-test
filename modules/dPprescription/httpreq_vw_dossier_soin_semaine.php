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

$date = mbGetValueFromGet("date");
$prescription_id = mbGetValueFromGet("prescription_id");

// Initialisations
$sejour = new CSejour();
$patient = new CPatient();

// Creation du tableau de dates
$dates = array(mbDate("-2 DAYS", $date), mbDate("-1 DAYS", $date), $date, mbDate("+1 DAYS", $date), mbDate("+2 DAYS", $date));

// Chargement de la prescription
$prescription = new CPrescription();
if($prescription_id){
  $prescription->load($prescription_id);
  $prescription->loadRefsLinesMed("1","1","service");
  $prescription->loadRefsLinesElementByCat("1","","service");
  $prescription->loadRefsPerfusions("1","service");
  foreach($prescription->_ref_perfusions as &$_perfusion){
    $_perfusion->loadRefsLines();
    $_perfusion->loadRefPraticien();
  }

  // Chargement du poids et de la chambre du patient
  $sejour =& $prescription->_ref_object;
  $sejour->loadRefPatient();
  $sejour->loadRefPraticien();
  $patient =& $sejour->_ref_patient;
  $patient->loadRefConstantesMedicales();

  // Calcul du plan de soin sur les 5 jours
  foreach($dates as $_date){
    $prescription->calculPlanSoin($_date, 0, 1);
  }
}

// Calcul du nombre de produits (rowspan)
$prescription->calculNbProduit();

// Chargement des transmissions qui ciblent les lignes de la prescription
$prescription->loadAllTransmissions();

// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("patient", $patient);
$smarty->assign("categories", $categories);
$smarty->assign("dates", $dates);
$smarty->assign("prescription", $prescription);
$smarty->assign("now", $date);
$smarty->assign("categorie", new CCategoryPrescription());
$smarty->display("../../dPprescription/templates/inc_vw_dossier_soin_semaine.tpl");

?>