<?php /* $Id: rpu.class.php 6716 2009-07-28 06:53:12Z mytto $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6716 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$today = date("d/m/Y");

$rpu_id = CValue::get("rpu_id", 0);
$offline = CValue::get("offline", 0);
$formulaires = null;

//Création du rpu
$rpu = new CRPU();
$rpu->load($rpu_id);

if ($offline) {
  $rpu->loadRefSejour();
}
else {
  $rpu->loadComplete();
}
$rpu->loadRefSejourMutation();

$sejour = $rpu->_ref_sejour;
$sejour->loadRefsConsultations();
$sejour->loadListConstantesMedicales();
$sejour->loadSuiviMedical();
$patient = $sejour->_ref_patient;
$patient->loadRefConstantesMedicales();
$patient->loadIPP();
$patient->loadRefDossierMedical();

$dossier_medical = $patient->_ref_dossier_medical;
$dossier_medical->countAntecedents();
$dossier_medical->loadRefPrescription();
$dossier_medical->loadRefsTraitements();

$consult = $sejour->_ref_consult_atu;
$consult->loadRefPatient();
$consult->loadRefPraticien();
$consult->loadRefsBack();
$consult->loadRefsDocs();
foreach ($consult->_ref_actes_ccam as $_ccam) {
  $_ccam->loadRefExecutant();
}


$constantes_medicales_grid = CConstantesMedicales::buildGrid($sejour->_list_constantes_medicales, false);

if (CModule::getActive("forms")) {
  $params = array(
    "detail" => 3,
    "reference_id" => $sejour->_id,
    "reference_class" => $sejour->_class,
    "target_element" => "ex-objects-$sejour->_id",
    "print" => 1,
  );

  $formulaires = CApp::fetch("forms", "ajax_list_ex_object", $params);
}

$dossier     = array();
$list_lines  = array();
$atc_classes = array();

if (CModule::getActive("dPprescription")){
  // Chargement du dossier de soins cloturé
  $prescription = new CPrescription();
  $prescription->object_class = "CSejour";
  $prescription->type = "sejour";
  $prescription->object_id = $sejour->_id;

  $prescription->loadMatchingObject();

  // Chargement des lignes
  $prescription->loadRefsLinesMedComments("1", "", "", "0", "1");
  $prescription->loadRefsLinesElementsComments();
  $prescription->loadRefsPrescriptionLineMixes();

  if (count($prescription->_ref_prescription_line_mixes)) {
    foreach($prescription->_ref_prescription_line_mixes as $_prescription_line_mix){
      $_prescription_line_mix->loadRefsLines();
      $_prescription_line_mix->calculQuantiteTotal();
      $_prescription_line_mix->loadRefPraticien();
      foreach($_prescription_line_mix->_ref_lines as $_perf_line){
        $list_lines["prescription_line_mix"][$_perf_line->_id] = $_perf_line;
        $_perf_line->loadRefsAdministrations();
        foreach($_perf_line->_ref_administrations as $_administration_perf){
          $_administration_perf->loadRefAdministrateur();
          if(!$_administration_perf->planification){
            $dossier[CMbDT::date($_administration_perf->dateTime)]["prescription_line_mix"][$_perf_line->_id][$_administration_perf->quantite][$_administration_perf->_id] = $_administration_perf;
          }
        }
      }
    }
  }

  // Parcours des lignes de medicament et stockage du dossier cloturé
  if (count($prescription->_ref_lines_med_comments["med"])) {
    foreach($prescription->_ref_lines_med_comments["med"] as $_atc => $lines_by_type){
      if(!isset($atc_classes[$_atc])){
        $classe_atc = new CBcbClasseATC();
        $atc_classes[$_atc] = $classe_atc->getLibelle($_atc);
      }
      foreach($lines_by_type as $med_id => $_line_med){
        $list_lines["medicament"][$_line_med->_id] = $_line_med;

        $_line_med->loadRefsAdministrations();
        foreach($_line_med->_ref_administrations as $_administration_med){
          $_administration_med->loadRefAdministrateur();
          if(!$_administration_med->planification){
            $dossier[CMbDT::date($_administration_med->dateTime)]["medicament"][$_line_med->_id][$_administration_med->quantite][$_administration_med->_id] = $_administration_med;
          }
        }
      }
    }
  }

  // Parcours des lignes d'elements
  if (count($prescription->_ref_lines_elements_comments)) {
    foreach($prescription->_ref_lines_elements_comments as $chap => $_lines_by_chap){
      foreach($_lines_by_chap as $_lines_by_cat){
        foreach($_lines_by_cat["comment"] as $_line_elt_comment){
          $_line_elt_comment->loadRefPraticien();
        }
        foreach($_lines_by_cat["element"] as $_line_elt){
          $list_lines[$chap][$_line_elt->_id] = $_line_elt;
          $_line_elt->loadRefsAdministrations();
          foreach($_line_elt->_ref_administrations as $_administration_elt){
            $_administration_elt->loadRefAdministrateur();
            if(!$_administration_elt->planification){
              $dossier[CMbDT::date($_administration_elt->dateTime)][$chap][$_line_elt->_id][$_administration_elt->quantite][$_administration_elt->_id] = $_administration_elt;
            }
          }
        }
      }
    }
  }
}
ksort($dossier);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("rpu"    , $rpu);
$smarty->assign("patient", $patient);
$smarty->assign("sejour" , $sejour);
$smarty->assign("consult", $consult);
$smarty->assign("today"  , $today  );
$smarty->assign("offline", $offline);
$smarty->assign("formulaires", $formulaires);
$smarty->assign("dossier", $dossier);
$smarty->assign("list_lines", $list_lines);
if(CModule::getActive("dPprescription")){
  $smarty->assign("prescription", $prescription);
}
$smarty->assign("formulaires", $formulaires);
$smarty->assign("praticien", new CMediusers);
$smarty->assign("atc_classes", $atc_classes);
$smarty->assign("dossier_medical", $dossier_medical);
$smarty->assign("constantes_medicales_grid", $constantes_medicales_grid);

$smarty->display("print_dossier.tpl");

?>