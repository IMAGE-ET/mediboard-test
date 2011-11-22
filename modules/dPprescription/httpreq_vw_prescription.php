<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m;

$can->needsRead();

$prescription_id = CValue::getOrSession("prescription_id");
$object_class    = CValue::get("object_class");
$object_id       = CValue::get("object_id");
$mode_pharma     = CValue::get("mode_pharma", 0);
$refresh_pharma  = CValue::get("refresh_pharma", 0);
$mode_protocole  = CValue::getOrSession("mode_protocole", 0);
$full_mode       = CValue::get("full_mode", 0);
$sejour_id       = CValue::getOrSession("sejour_id");
$chir_id         = CValue::getOrSession("chir_id");
$anesth_id       = CValue::getOrSession("anesth_id");
$operation_id    = CValue::get("operation_id");
$type            = CValue::getOrSession("type");
$element_id      = CValue::getOrSession("element_id");
$chapitre        = CValue::getOrSession("chapitre", "medicament");
$mode_anesth     = CValue::getOrSession("mode_anesth");
$pratSel_id      = CValue::getOrSession("pratSel_id");
$mode_sejour     = CValue::getOrSession("mode_sejour", false);
$praticien_for_prot_id = CValue::getOrSession("praticien_for_prot_id");
$hide_old_lines  = CValue::get("hide_old_lines");
$hide_header = CValue::get("hide_header", "0");

$hidden_lines_count = 0;

// Recuperation de l'operation_id stocké en session en salle d'op
if(!$operation_id && !$mode_sejour){
  $operation_id = @$_SESSION["dPsalleOp"]["operation_id"];
  CValue::setSession("operation_id", $operation_id);
}
	
// Gestion du mode d'affichage 
$praticien_sortie_id = CValue::getOrSession("praticien_sortie_id");

// Initialisations
$protocoles_praticien = array();
$protocoles_function  = array();
$packs_praticien      = array();
$packs_function       = array();
$poids                = "";
$alertesAllergies     = array();
$alertesInteractions  = array();
$alertesIPC           = array();
$alertesProfil        = array();
$alertesPosologie     = array();
$favoris              = array();
$listProduits         = array();
$historique           = array();
$executants           = array();
$protocoles_ids       = array();
$dossier_medical      = new CDossierMedical();

// Chargement de l'utilisateur courant
$current_user = CMediusers::get();
$is_praticien = $current_user->isPraticien();
$current_user->isInfirmiere();

// Liste des praticiens disponibles
$listPrats = $is_praticien ? null : $current_user->loadPraticiens(PERM_EDIT);

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// Calcul de la div à rafraichir
if ($element_id){
  $element = new CElementPrescription();
  $element->load($element_id);
  $element->loadRefCategory();
  $chapitre = $element->_ref_category_prescription->chapitre;
}

// Chargement de la prescription
$prescription = new CPrescription();
if($prescription_id){
  $prescription->load($prescription_id);
}

// Si pas de prescription_id et presence d'un sejour_id => chargement de la prescription de sejour
$prescriptions_sejour = array();
if(!$prescription->_id && $sejour_id && !$mode_protocole){
	$prescription_sejour = new CPrescription();
	$where = array();
  $where["object_id"] = " = '$sejour_id'";
  $where["object_class"] = " = 'CSejour'";
  $where["type"] = " != 'traitement'";
  $order = "prescription_id DESC";
  $prescriptions_sejour = $prescription_sejour->loadList($where, $order);
  if(count($prescriptions_sejour)){
    $prescription =& end($prescriptions_sejour);
  }
  foreach($prescriptions_sejour as $_prescription_sejour){
  	if($_prescription_sejour->type == "sejour"){
  		$prescription =& $_prescription_sejour;
  		break;
  	}
  }
}

// Si tous les elements sont passés, on charge la prescription (cas de la prescription externe)
// Permet de ne pas recreer une prescription si elle existe déja... en cas de non rechargement de la widget
if(!$prescription_id && $object_class && $object_id && $type){
	$full_mode = 1;
  $prescription = new CPrescription();
  $prescription->object_id = $object_id;
  $prescription->object_class = $object_class;
  $prescription->type = $type;	
  $prescription->loadMatchingObject();
}

if(!isset($hide_old_lines)){
  if($prescription->type == "sejour" && $prescription->object_id && !$prescription->_ref_object->sortie_reelle){
    $hide_old_lines = CAppUI::pref('hide_old_lines');
  } else {
    $hide_old_lines = "0";  
  }
}

$favoris_praticien_id = "";
if($prescription->object_id && $prescription->_current_praticien_id){
  $favoris_praticien_id = $prescription->_current_praticien_id;
} else {
  // Dans le cas d'un protocole
  // Si le protocole appartient à un praticien, on charge les favoris du praticien
  if($prescription->praticien_id){
    $favoris_praticien_id = $prescription->praticien_id;
  } else {
  // Sinon, on charge les favoris du user courant si c'est un praticien (protocole de cabinet et d'etablissement)
    if($is_praticien){
      $favoris_praticien_id = $current_user->user_id;
    }
  }
}
 
// Chargement des categories pour chaque chapitre
$categories = ($full_mode || $chapitre != "medicament") ? CCategoryPrescription::loadCategoriesByChap() : null;

// Chargement des lignes de la prescription et des droits sur chaque ligne
if($prescription->_id){
	if($prescription->type == "sejour" && $prescription->object_id){
		if(CGroups::loadCurrent()->_id != $prescription->_ref_object->group_id){
		  CAppUI::stepAjax("Ce séjour n'est pas dans l'établissement courant", UI_MSG_WARNING);
		  return;
		}
	}
	
	// Calcul des planifs systemes
	$prescription->calculAllPlanifSysteme();

  // Chargement de la photo d'identité du patient
	$prescription->loadRefPatient();
  $patient =& $prescription->_ref_patient;
  $patient->loadRefPhotoIdentite();
	$patient->loadRefConstantesMedicales();

  // Chargement des praticiens prescripteurs
  $prescription->getPraticiens();

  // Chargement de l'historique
  $historique = $prescription->loadRefsLinesHistorique();
   	
  // Calcul du nombre d'elements dans la prescription
	$prescription->countLinesMedsElements($praticien_sortie_id, $operation_id);

	// Chargement des medicaments et commentaires de medicament
	if ($full_mode || $chapitre == "medicament" || $chapitre == "inscription" || $mode_protocole || $mode_pharma) {
		
		// Chargement des inscriptions (med + elt)
		$prescription->loadRefsLinesInscriptions();
		foreach($prescription->_ref_lines_inscriptions as $_incriptions_by_type){
			foreach($_incriptions_by_type as $_inscription){
				$_inscription->loadRefsPrises();
				$_inscription->getAdvancedPerms($is_praticien, $mode_protocole, $mode_pharma, $operation_id);
			}
		}
		
		// Chargement des medicaments
		$prescription->loadRefsLinesMedComments();
	  foreach($prescription->_ref_lines_med_comments as $type => $lines_by_type){
	  	foreach($lines_by_type as $med_id => $_line_med){
	  	  if($hide_old_lines && $_line_med->_fin_reelle && mbDate($_line_med->_fin_reelle) < mbDate()){
	  	    unset($prescription->_ref_lines_med_comments[$type][$med_id]);
					$hidden_lines_count++;
	  	  }
				$protocoles_ids[] = $_line_med->protocole_id;
	  		$_line_med->getAdvancedPerms($is_praticien, $mode_protocole, $mode_pharma, $operation_id);
	  	  if($_line_med->_class == "CPrescriptionLineMedicament"){
	  	    $_line_med->countBackRefs("administration");
	  	    $_line_med->loadRefsVariantes();
					$_line_med->loadRefParentLine();
	  	  }
	  	}
	  }
		
		// Chargement des prescription_line_mixes
	  $prescription->loadRefsPrescriptionLineMixes();
		if($prescription->_ref_prescription_line_mixes_by_type){
		  foreach($prescription->_ref_prescription_line_mixes_by_type as $_type_line => $_lines_mixes){
		  	foreach($_lines_mixes as $_line_mix_id => $_prescription_line_mix){
		  	  if($hide_old_lines && $_prescription_line_mix->_date_fin && mbDate($_prescription_line_mix->_date_fin) < mbDate()){
		        unset($prescription->_ref_prescription_line_mixes_by_type[$_type_line][$_line_mix_id]);            
						$hidden_lines_count++;
		      }
					$protocoles_ids[] = $_prescription_line_mix->protocole_id;
			    $_prescription_line_mix->loadRefPraticien();
			    $_prescription_line_mix->loadRefsLines();
			    $_prescription_line_mix->loadRefParentLine();
			    $_prescription_line_mix->getAdvancedPerms($is_praticien, $mode_protocole, $mode_pharma, $operation_id);
		    }
			}
		}
		
	  // Pour une prescription non protocole
	  if ($prescription->object_id) {
	    $object =& $prescription->_ref_object;
			// Chargement du patient
		  $object->loadRefPatient();
			$object->loadRefsNotes();
			$patient =& $object->_ref_patient;
			$patient->loadRefDossierMedical();
			$object->loadRefsPrescriptions();
			
			// Chargement du dossier medicam
		  $dossier_medical =& $patient->_ref_dossier_medical;
		  $dossier_medical->updateFormFields();
		  $dossier_medical->loadRefsAntecedents();
		  $dossier_medical->loadRefsTraitements();
		  $dossier_medical->countAntecedents();
		  $dossier_medical->countAllergies();
		  
		  // Calcul des alertes de la prescription
			if(CModule::getActive("bcb")){
			  $allergies    = new CBcbControleAllergie();
			  $allergies->setPatient($patient);
			  $profil       = new CBcbControleProfil();
			  $profil->setPatient($patient);
      }
		}
	
	  if(CModule::getActive("bcb")){
	    $interactions = new CBcbControleInteraction();
		  $IPC          = new CBcbControleIPC();
	    $surdosage    = new CBcbControleSurdosage();
	    $surdosage->setPrescription($prescription);
	  
			$list_cip_perf = array();
		  foreach($prescription->_ref_prescription_line_mixes as $_prescription_line_mix){
		    foreach($_prescription_line_mix->_ref_lines as $_perf_line){
		      if(!in_array($_perf_line->code_cip, $list_cip_perf)){
						$list_cip_perf[] = $_perf_line->code_cip;
						if($prescription->object_id){
			        $allergies->addProduit($_perf_line->code_cip);
			        $profil->addProduit($_perf_line->code_cip);
			      }			    
				    $interactions->addProduit($_perf_line->code_cip);
				    $IPC->addProduit($_perf_line->code_cip);
					}
		    }
		  }
	
			$list_cip_med = array();
		  foreach($prescription->_ref_prescription_lines as &$line) {
		  	if(!in_array($line->code_cip, $list_cip_med)){
			  	$list_cip_med[] = $line->code_cip;
			    if($prescription->object_id){
			      $allergies->addProduit($line->code_cip);
			      $profil->addProduit($line->code_cip);
			    }			    
			    $interactions->addProduit($line->code_cip);
			    $IPC->addProduit($line->code_cip);
				}
		  }
	
		  if($prescription->object_id){
		    $alertesAllergies    = $allergies->getAllergies();
		    $alertesProfil       = $profil->getProfil();
			  $alertesPosologie    = $surdosage->getSurdosage();
	    }		  
	    $alertesInteractions = $interactions->getInteractions();
		  $alertesIPC          = $IPC->getIPC();
		}
	  if(!$prescription->object_id){
	    $prescription->_alertes["allergie"] = array();
	    $prescription->_alertes["profil"] = array(); 
	  }

	  $prescription->_scores["hors_livret"] = 0;
    foreach($prescription->_ref_prescription_lines as &$line) {
      if($prescription->object_id){
        $prescription->checkAllergies($alertesAllergies, $line->code_cip);
        $prescription->checkProfil($alertesProfil, $line->code_cip);
        $prescription->checkPoso($alertesPosologie, $line->code_cip);
		  }		      
      $prescription->checkIPC($alertesIPC, $line->code_cip);
      $prescription->checkInteractions($alertesInteractions, $line->code_cip);
      if(!$line->_ref_produit->inLivret && $prescription->type == "sejour"){
        $prescription->_scores["hors_livret"]++;
      }
    }
		
	  foreach($prescription->_ref_prescription_line_mixes as $_prescription_line_mix){
	  	foreach($_prescription_line_mix->_ref_lines as $_perf_line){
	  	  if($prescription->object_id){
	  	    $prescription->checkAllergies($alertesAllergies, $_perf_line->code_cip);
	  	    $prescription->checkProfil($alertesProfil, $_perf_line->code_cip);
	  	  }			    
		    $prescription->checkIPC($alertesIPC, $_perf_line->code_cip);
		    $prescription->checkInteractions($alertesInteractions, $_perf_line->code_cip);
		    if(!$_perf_line->_ref_produit->inLivret && $prescription->type == "sejour"){
		      $prescription->_scores["hors_livret"]++;
		    }
		  }
	  }
    
	  $prescription->loadRefsLinesElementsComments();
	  if(count($prescription->_ref_lines_elements_comments)){
      foreach($prescription->_ref_lines_elements_comments as $name_chap => $cat_by_chap){
        foreach($cat_by_chap as $name_cat => $lines_by_cat){
          foreach($lines_by_cat as $type_elt => $lines_by_type){
            foreach($lines_by_type as $key => $_line){
              $protocoles_ids[] = $_line->protocole_id;
            }
          }
        }
      }
	  }
    
		if($prescription->object_id){
		  $score_prescription = 0;
		  foreach($prescription->_scores as $type_score => $_score){
		    // Si _score est un array (interaction, profil ou posologie)
		    if(is_array($_score)){
		      if(array_key_exists("niveau_max", $_score)){
		        $niveau_max = "niv".$_score["niveau_max"];
		        $score_prescription = max($score_prescription, CAppUI::conf("dPprescription CPrescription scores $type_score $niveau_max"));
		      } elseif ($type_score == "allergie") {
		        // allergies
		        if(count($_score)){
	            $score_prescription = max($score_prescription, CAppUI::conf("dPprescription CPrescription scores $type_score"));
	          }
		      }
		    } elseif($_score > 0) {
		      $score_prescription = max($score_prescription, CAppUI::conf("dPprescription CPrescription scores $type_score"));
		    }
		  }
		  $prescription->_score_prescription = $score_prescription;
	    
			// Sauvegarde du score de la prescription dans une nouvel objet (permet d'eviter de perdre le chargement des refs)
			$new_prescription = new CPrescription();
			$new_prescription->load($prescription->_id);
			$new_prescription->score = $score_prescription;
			$new_prescription->store();
			
			// Chargement du poids du patient
			$patient->loadRefConstantesMedicales();
			
		  $constantes_medicales = $patient->_ref_constantes_medicales;
		  $poids = $constantes_medicales->poids;
		
		  if($object->_class == "CSejour"){
		    $whereOp = array();
		    $whereOp["annulee"] = " = '0'";
		    $object->loadRefsOperations($whereOp);
		    foreach($object->_ref_operations as $_operation){
		      $_operation->loadRefPlageOp();
		      $prescription->_dates_dispo[$_operation->_id] = $_operation->_datetime;
		    }
		  }
		}
	} else {
		// Chargement des executants
		$executants["externes"] = CExecutantPrescriptionLine::getAllExecutants();
		$executants["users"] = CFunctionCategoryPrescription::getAllUserExecutants();

		// Chargement des lignes de DMI
	  if ($chapitre === 'dmi' && CAppUI::conf("dmi CDMI active") && CModule::getActive('dmi')) {
	    $prescription->loadRefsLinesDMI($operation_id);
	    foreach($prescription->_ref_lines_dmi as $_line_dmi){
	      $_line_dmi->loadRefsFwd();
        $_line_dmi->loadRefProductOrderItemReception()->loadRefOrderItem();
	    }
	  }
	  
	  // Chargement des elements et commentaires d'elements
	  $prescription->loadRefsLinesElementsComments("0", "1", $chapitre);
	  if(count($prescription->_ref_lines_elements_comments)){
	    foreach($prescription->_ref_lines_elements_comments as $name_chap => $cat_by_chap){
	      foreach($cat_by_chap as $name_cat => $lines_by_cat){
	        foreach($lines_by_cat as $type_elt => $lines_by_type){
	          foreach($lines_by_type as $key => $_line){
	          	if($_line->child_id){
	          		unset($prescription->_ref_lines_elements_comments[$name_chap][$name_cat][$type_elt][$key]);
	          	}
							
							if($hide_old_lines && $_line->_fin_reelle && mbDate($_line->_fin_reelle) < mbDate()){
				        unset($prescription->_ref_lines_elements_comments[$name_chap][$name_cat][$type_elt][$key]);
								$hidden_lines_count++;
				      }
			
	          	if($_line->_class == "CPrescriptionLineElement"){
                $_line->loadRefsPrises();
								$_line->loadRefDM();
								$_line->loadRefParentLine();
              }
							$_line->getAdvancedPerms($is_praticien, $mode_protocole, $mode_pharma, $operation_id);
	          }
	        }
	      }
	    }
	  }   
	}
}

if($mode_protocole){
	// Chargement de la liste des praticiens
  $praticien = new CMediusers();
  $praticiens = $praticien->loadPraticiens();

  // Chargement des functions
  $function = new CFunctions();
  $functions = $function->loadSpecialites(PERM_EDIT);
  
  // Chargement des etablissement
  $groups = CGroups::loadGroups(PERM_EDIT);
}

$protocole_line = new CPrescriptionLineMedicament();
if ($prescription->type != "externe" || ($prescription->type == "externe" && !CAppUI::pref("date_empty_externe"))) {
  $protocole_line->debut = mbDate();
}

$contexteType = array(
  "CConsultation" => array("externe"),
  "CSejour"       => array("pre_admission", "sortie", "sejour"),
);

// Liste d'heures et de minutes pour l'arret des lignes
$hours = range(0,23);
foreach($hours as &$hour){
	$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
}
$mins = range(0,59);
foreach($mins as &$min){
	$min = str_pad($min, 2, "0", STR_PAD_LEFT);
}

$filter_line = new CPrescriptionLineMedicament();
if ($prescription->type != "externe" || ($prescription->type == "externe" && !CAppUI::pref("date_empty_externe"))){
  $filter_line->debut = mbDate();
	$filter_line->time_debut = mbTransformTime(null, null, "%H:00:00");
}

$prise = new CPrisePosologie();
$prise->quantite = 1.0;

$protocoles_ids = array_unique($protocoles_ids);
CMbArray::removeValue(null, $protocoles_ids);
$protocoles_ids = array_flip($protocoles_ids);

foreach ($protocoles_ids as $prot_id => $prot) {
  $prot = new CPrescription;
  $prot->load($prot_id);
  $protocoles_ids[$prot_id] = $prot;
}

array_multisort(CMbArray::pluck($protocoles_ids, "libelle"), SORT_ASC, $protocoles_ids);

// Chargement des aides
$prescriptionLineMedicament = new CPrescriptionLineMedicament();
$prescriptionLineElement = new CPrescriptionLineElement();
$prescription_line_mix = new CPrescriptionLineMix();

$_sejour = new CSejour();
$_sejour->load($sejour_id);

$operation = new COperation();
if($operation->load($operation_id)) {
  $operation->loadRefPlageOp();
  $operation->_ref_anesth->loadRefFunction();
}

if (!$prescription->_id) {
	$_sejour->loadRefsOperations();
  $operations = $_sejour->_ref_operations;
	foreach ($operations as $_operation)
	  $_operation->loadRefPlageOp();
}

$_chir_id   = $chir_id   ? $chir_id : ($current_user->isPraticien() ? $current_user->user_id : $_sejour->praticien_id);
$_anesth_id = $anesth_id ? $anesth_id : ($current_user->isFromType(array("Anesthésiste")) ? 
                                            $current_user->user_id : 
                                            ($operation->_id ? $operation->_ref_plageop->anesth_id : null));

$_chir = new CMediusers();
$_anesth = new CMediusers();

if ($_chir_id) {
	$_chir->load($_chir_id);
	$_chir->loadRefFunction();
}

if ($_anesth_id) {
	$_anesth->load($_anesth_id);
	$_anesth->loadRefFunction();
}

if(isset($operation->_ref_anesth->_id)){
  unset($listPrats[$operation->_ref_anesth->_id]);
}
if(isset($prescription->_ref_current_praticien->_id)){
  unset($listPrats[$prescription->_ref_current_praticien->_id]);
}

// Classement des lignes par ordre alphabetique
function compareMed($line1, $line2){
  return strcmp($line1->_ucd_view, $line2->_ucd_view);
}
if(isset($prescription->_ref_lines_med_comments["med"])){
  usort($prescription->_ref_lines_med_comments["med"], "compareMed");
}



// Multiple prescriptions existante pour le séjour
$prescription_multiple = new CPrescription;
$where = array(
  "type" => " = 'sejour'",
  "object_class" => " = 'CSejour'",
  "object_id" => " = '$prescription->object_id'"
);

$multiple_prescription = $prescription_multiple->loadIds($where);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mode_substitution", 0);

// Mode permettant de supprimer qq elements de la ligne en salle d'op (Anesthesie)
$smarty->assign("current_user"         , $current_user);
$smarty->assign("mode_anesth"          , $mode_anesth);
$smarty->assign("historique"           , $historique);
$smarty->assign("filter_line"          , $filter_line);
$smarty->assign("hours"                , $hours);
$smarty->assign("mins"                 , $mins);
$smarty->assign("praticien_sortie_id"  , $praticien_sortie_id);
$smarty->assign("contexteType"         , $contexteType);
$smarty->assign("httpreq"              , 1);
$smarty->assign("sejour_id"            , $sejour_id);
$smarty->assign("is_praticien"         , $is_praticien);
$smarty->assign("today"                , mbDate());
$smarty->assign("now"                  , mbDateTime());
$smarty->assign("poids"                , $poids);
$smarty->assign("categories"           , $categories);
$smarty->assign("executants"           , $executants);
$smarty->assign("moments"              , $moments);
$smarty->assign("prise_posologie"      , $prise);
$smarty->assign("protocole"            , new CPrescription());
$smarty->assign("alertesAllergies"     , $alertesAllergies);
$smarty->assign("alertesInteractions"  , $alertesInteractions);
$smarty->assign("alertesIPC"           , $alertesIPC);
$smarty->assign("alertesProfil"        , $alertesProfil);
$smarty->assign("prescription"         , $prescription);
$smarty->assign("listPrats"            , $listPrats);
$smarty->assign("favoris_praticien_id" , $favoris_praticien_id);
$smarty->assign("category"             , $chapitre);
$smarty->assign("class_category"       , new CCategoryPrescription());
$smarty->assign("refresh_pharma"       , $refresh_pharma);
$smarty->assign("mode_pharma"          , $mode_pharma);
$smarty->assign("full_mode"            , $full_mode);
$smarty->assign("protocole_line"       , $protocole_line);
$smarty->assign("mode_protocole"       , $mode_protocole);
$smarty->assign("prescriptions_sejour" , $prescriptions_sejour);
$smarty->assign("dossier_medical"      , $dossier_medical);
$smarty->assign("now_time"             , mbTime());
$smarty->assign("mode_pack"            , "0");
$smarty->assign("prescription_line_mix", new CPrescriptionLineMix());
$smarty->assign("operation_id"         , $operation_id);
$smarty->assign("pratSel_id"           , $pratSel_id);
$smarty->assign("mode_sejour"          , $mode_sejour);
$smarty->assign("praticien_for_prot_id", $praticien_for_prot_id);
$smarty->assign("user_id"              , $current_user->user_id);
$smarty->assign("hide_old_lines"       , $hide_old_lines);
$smarty->assign("hidden_lines_count"   , $hidden_lines_count);
$smarty->assign("hide_header"          , $hide_header);
$smarty->assign("sejour"               , $_sejour);
$smarty->assign("multiple_prescription", $multiple_prescription);
$smarty->assign("admin_prescription"   , CModule::getCanDo("dPprescription")->admin || CMediusers::get()->isPraticien());
$smarty->assign("protocoles_ids"       , $protocoles_ids);

if($full_mode){
  $smarty->assign("praticien_sejour", $_sejour->praticien_id);
  $smarty->assign("chir"   , $_chir);
  $smarty->assign("anesth"   , $_anesth);
  $smarty->assign("operation", $operation);
  if (!$prescription->_id) {
    $smarty->assign("operations", $operations);
  }
  $smarty->display("vw_edit_prescription_popup.tpl");
  return;
}

if($mode_protocole){
	$smarty->assign("function_id", "");
  $smarty->assign("praticien_id", "");
  $smarty->assign("group_id", "");
  $smarty->assign("praticiens", $praticiens);
  $smarty->assign("functions", $functions);
  $smarty->assign("groups", $groups);
  $smarty->assign("category", "medicament");
  $smarty->display("inc_vw_prescription.tpl");
  return;
}

// Premier chargement de la pharmacie
if($mode_pharma && $refresh_pharma){
  $smarty->assign("praticien", $prescription->_ref_praticien);
  $smarty->display("inc_vw_prescription.tpl");	
  return;
}

if(!$refresh_pharma && !$mode_protocole){
  // Refresh Pharma
  if($mode_pharma){
  	$chapitre = "medicament";
	  $smarty->display("inc_div_medicament.tpl");
	} else {
	  // Refresh Protocole
    if(!$chapitre){
	  	$smarty->display("inc_vw_produits_elements.tpl");	
    } else {
      // Refresh Medicament
      if($chapitre == "medicament" || $chapitre == "inscription"){
       	$smarty->display("inc_div_$chapitre.tpl");
      }
      // refresh Element
      else {
        $smarty->assign("element", $chapitre);
        if (CAppUI::conf("dmi CDMI active") && CModule::getActive('dmi') && $chapitre == 'dmi') {
        	$smarty->display("../../dmi/templates/inc_prescription_dmi.tpl");
        }
        else {
          $smarty->display("inc_div_element.tpl");
        }
      }
    }
  }
}


?>