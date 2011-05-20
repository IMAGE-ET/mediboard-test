<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Recuperation du guid de la ligne à charger
$line_guid = CValue::get("line_guid");

// Recuperation des autres parametres
$mode_protocole = CValue::get("mode_protocole");
$mode_pharma    = CValue::get("mode_pharma");
$operation_id   = CValue::get("operation_id");
$mode_substitution = CValue::get("mode_substitution");
$advanced_prot  = CValue::get("advanced_prot");

$aides_prescription = array();
$executants = array();
$category_id = 0;
$dossier_medical = array();

// Chargement de la ligne
$line = CMbObject::loadFromGuid($line_guid);

// Chargement de l'utilisateur courant
$current_user = new CMediusers();
$current_user->load(CAppUI::$user->_id);
$is_praticien = $current_user->isPraticien();
	
$line->getAdvancedPerms($is_praticien, $mode_protocole, $mode_pharma, $operation_id);

// Chargement des infos sur le patient
$line->_ref_prescription->loadRefObject();
$object = $line->_ref_prescription->_ref_object;
$object->loadRefPatient();
$patient = $object->_ref_patient;
$patient->loadRefPhotoIdentite();
$patient->loadRefConstantesMedicales();


if($line->_ref_prescription->type == "sejour"){
  $patient->loadRefDossierMedical();
	  
	// Chargement du dossier medical
	$dossier_medical = $patient->_ref_dossier_medical;
	$dossier_medical->updateFormFields();
	$dossier_medical->loadRefsAntecedents();
	$dossier_medical->loadRefsTraitements();
	$dossier_medical->countAntecedents();
	$dossier_medical->countAllergies();	
	
	// Chargement de l'affectation courante
	$sejour = $line->_ref_prescription->_ref_object;
	$sejour->loadRefCurrAffectation();
}
			
			
if($line instanceof CPrescriptionLineMedicament){
  // Chargement des ref de la ligne
	$line->loadRefsPrises();

  $_prat_id = !$line->_ref_prescription->object_id ? $line->_ref_prescription->praticien_id : null;
   
   $line->loadRefProduitPrescription();
   if(!$line->_ref_produit_prescription->_id){
     $line->loadMostUsedPoso(null, $_prat_id);
     $line->loadRefsFwd();
     $line->_ref_produit->loadVoies();
     $line->isPerfusable();
   } else {
     $line->_unites_prise[] = $line->_ref_produit_prescription->unite_prise;
     $line->_ref_produit->voies[] = $line->_ref_produit_prescription->voie;
   }
  
   $line->countBackRefs("administration");
   $line->loadRefsSubstitutionLines();
   
   foreach($line->_ref_substitution_lines["CPrescriptionLineMedicament"] as $_line_med) {
     $_line_med->loadRefsPrises();
   }
   
   $line->loadRefParentLine(); 
	 
	 if($line->_is_perfusable){
	   if($mode_substitution){
	  	 $line->_ref_prescription->loadRefsPrescriptionLineMixes("",0,0);
	   } else {
	 	   $line->_ref_prescription->loadRefsPrescriptionLineMixes();
	   }
		 foreach($line->_ref_prescription->_ref_prescription_line_mixes as $_prescription_line_mix){
        $_prescription_line_mix->loadRefsLines();
        $_prescription_line_mix->loadVoies();
      }
	 }
}

if($line instanceof CPrescriptionLineElement){
  $category_id = $line->_ref_element_prescription->_ref_category_prescription->_id;
  $line->loadRefsPrises();
  $line->loadRefDM();
}

if ($line instanceof CPrescriptionLineComment) {
  $category_id = $line->_ref_category_prescription->_id;
}

if ($line instanceof CPrescriptionLineElement || $line instanceof CPrescriptionLineComment) {
  // Chargement des executants
  $executants["externes"] = CExecutantPrescriptionLine::getAllExecutants();
  $executants["users"] = CFunctionCategoryPrescription::getAllUserExecutants();
}

if($line instanceof CPrescriptionLineMix){
  // Chargement des ref de la ligne
  $line->loadRefPraticien();
  $line->loadRefsLines();
  $line->loadRefParentLine();
	$line->loadRefsSubstitutionLines();
  $line->loadVoies(); 

  if($line->_ref_lines){
    foreach($line->_ref_lines as &$line_perf){
      $line_perf->loadRefsFwd();
    }
  }		 
}

// Chargement du praticien_id pour le chargement des aides
$_aide_praticien_id = $line->_protocole ? CAppUI::$user->_id : $line->praticien_id;

$line->loadAides($_aide_praticien_id);
$aides_prescription[$_aide_praticien_id][$line->_class_name] = $line->_aides["commentaire"]["no_enum"];

// Instanciation d'une prise
$prise_posologie = new CPrisePosologie();
$prise_posologie->quantite = 1;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("line", $line);
$smarty->assign("prescription", $line->_ref_prescription);

$smarty->assign("mode_protocole", $mode_protocole);
$smarty->assign("mode_pharma", $mode_pharma);
$smarty->assign("operation_id", $operation_id);

$smarty->assign("now", mbDateTime());
$smarty->assign("prise_posologie", $prise_posologie);
$smarty->assign("is_praticien", $is_praticien);

$smarty->assign("mode_pack", 0);
$smarty->assign("mode_substitution", $mode_substitution);
$smarty->assign("aides_prescription", $aides_prescription);

if ($advanced_prot) {
  $smarty->assign("advanced_prot", $advanced_prot);
}
$smarty->assign("executants", $executants);
$smarty->assign("category_id", $category_id);

// Selection du template en fonction du type de ligne
switch ($line->_class_name) {
	case "CPrescriptionLineMedicament":
      $smarty->display("inc_vw_line_medicament.tpl");
			break;
	case "CPrescriptionLineElement":
      $smarty->display("inc_vw_line_element.tpl");
			break;
	case "CPrescriptionLineMix":
      $smarty->display("inc_vw_line_mix.tpl");
			break;
	case "CPrescriptionLineComment":
		  $smarty->display("inc_vw_line_comment.tpl");
			break;		
}

?>