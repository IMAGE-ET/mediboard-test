<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

set_min_memory_limit("640M");
set_time_limit(120);

$service_id = CValue::get("service_id");
$date       = CValue::get("date", mbDate());
$mode       = CValue::get("mode", 0);

$service = new CService;
$service->load($service_id);
loadServiceComplet($service, $date, $mode);

$sejours = array();
$dossiers_complets = array();

foreach($service->_ref_chambres as &$_chambre){
  foreach($_chambre->_ref_lits as &$_lits){
    foreach($_lits->_ref_affectations as &$_affectation){
      $sejour = $_affectation->_ref_sejour;
      $sejours[]= $sejour;
      
      // Détail du séjour
      $sejour->checkDaysRelative($date);
      $sejour->loadNDA();
      $sejour->loadRefsNotes();
      $sejour->loadRefCurrAffectation()->loadRefLit();
      
      // Patient
      $patient = $sejour->loadRefPatient();
      $patient->loadIPP();
      
      
      // Prescription
      if(CModule::getActive("dPprescription")){
        $prescription = $sejour->loadRefPrescriptionSejour();
        $prescription->loadRefsLinesElementByCat();
        $prescription->countFastRecentModif();
      }
      
      $params = array(
        "sejour_id" => $sejour->_id,
        "dialog" => 1,
        "offline" => 1,
        "in_modal" => 1
      );
      
      $dossiers_complets[$sejour->_id] = CApp::fetch("soins", "print_dossier_soins", $params);
    }
  }
}

$smarty = new CSmartyDP;
$smarty->assign("date", $date);
$smarty->assign("hour", mbTime());
$smarty->assign("service", $service);
$smarty->assign("sejours", $sejours);
$smarty->assign("dossiers_complets", $dossiers_complets);
$smarty->display("offline_sejours.tpl");

?>