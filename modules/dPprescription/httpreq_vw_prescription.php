<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
     
global $AppUI, $can, $m;

$can->needsRead();

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$object_class    = mbGetValueFromGet("object_class");
$object_id       = mbGetValueFromGet("object_id");
$mode_pharma     = mbGetValueFromGet("mode_pharma", 0);
$refresh_pharma  = mbGetValueFromGet("refresh_pharma", 0);
$mode_protocole  = mbGetValueFromGetOrSession("mode_protocole", 0);
$full_mode       = mbGetValueFromGet("full_mode", 0);
$sejour_id       = mbGetValueFromGetOrSession("sejour_id");
$chir_id         = mbGetValueFromGetOrSession("chir_id");
$anesth_id       = mbGetValueFromGetOrSession("anesth_id");
$operation_id    = mbGetValueFromGetOrSession("operation_id");
$type            = mbGetValueFromGetOrSession("type");
$element_id      = mbGetValueFromGetOrSession("element_id");
$chapitre        = mbGetValueFromGetOrSession("chapitre", "medicament");
$mode_anesth     = mbGetValueFromGetOrSession("mode_anesth");
$pratSel_id      = mbGetValueFromGetOrSession("pratSel_id");
$mode_sejour     = mbGetValueFromGetOrSession("mode_sejour", false);

// Recuperation de l'operation_id stocké en session en salle d'op
if(!$operation_id && !$mode_sejour){
  $operation_id = @$_SESSION["dPsalleOp"]["operation_id"];
  mbSetValueToSession("operation_id", $operation_id);
}

// Gestion du mode d'affichage
$readonly        = mbGetValueFromGetOrSession("readonly", 1);
$lite            = mbGetValueFromGetOrSession("lite", $AppUI->user_prefs["mode_readonly"] ? 0 : 1);
$full_line_guid  = mbGetValueFromGetOrSession("full_line_guid"); 

$praticien_sortie_id    = mbGetValueFromGetOrSession("praticien_sortie_id");

$historique = array();
// Initialisations
$protocoles_praticien = array();
$protocoles_function  = array();
$packs_praticien      = array();
$packs_function       = array();
$listFavoris          = array();
$poids                = "";
$alertesAllergies     = array();
$alertesInteractions  = array();
$alertesIPC           = array();
$alertesProfil        = array();
$alertesPosologie     = array();
$favoris              = array();
$listProduits         = array();
$dossier_medical = new CDossierMedical();

// Chargement de l'utilisateur courant
$user = new CMediusers();
$user->load($AppUI->user_id);
$is_praticien = $user->isPraticien();

// Liste des praticiens disponibles
$listPrats = $is_praticien ? null : $user->loadPraticiens(PERM_EDIT);

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// Chargement des executants
$executants["externes"] = CExecutantPrescriptionLine::getAllExecutants();
$executants["users"] = CFunctionCategoryPrescription::getAllUserExecutants();

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
 
// Chargement des categories pour chaque chapitre
/* Ici, chargement de toutes les categories pour permettre de visualiser correctement les prescriptions 
 * qui ont ete faites avant que les categories soient associées à un group_id
 */
$categories = $full_mode || $chapitre != "medicament" ? CCategoryPrescription::loadCategoriesByChap() : null;

// Chargement des lignes de la prescription et des droits sur chaque ligne
if($prescription->_id){
  $prescription->loadRefsPerfusions();
  
  $patient =& $prescription->_ref_patient;
  $patient->loadRefPhotoIdentite();
  
	foreach($prescription->_ref_perfusions as $_perfusion){
	  $_perfusion->loadRefsSubstitutionLines(); 
    $_perfusion->loadRefsLines();
	  $_perfusion->getAdvancedPerms($is_praticien, $mode_protocole, $mode_pharma, $operation_id);
	  $_perfusion->loadRefPraticien();
	  $_perfusion->_ref_praticien->loadRefFunction();
	  $_perfusion->loadRefParentLine();
	  if($_perfusion->_ref_lines){
		  foreach($_perfusion->_ref_lines as &$line_perf){
		    $line_perf->loadRefsFwd();
		  }
	  }
	}

  $prescription->getPraticiens();
  
	// Chargement de l'historique
  $historique = $prescription->loadRefsLinesHistorique();
	
  // Chargement des lignes de DMI
  if (CAppUI::conf("dmi CDMI active") && CModule::getActive('dmi') && $chapitre == 'dmi') {
    $prescription->loadRefsLinesDMI();
    foreach($prescription->_ref_lines_dmi as $_line_dmi){
      $_line_dmi->loadRefsFwd();
    }
  }
  
  // Chargement des elements et commentaires d'elements
  $prescription->loadRefsLinesElementsComments("", $chapitre);
  if(count($prescription->_ref_lines_elements_comments)){
	  foreach($prescription->_ref_lines_elements_comments as $name_chap => $cat_by_chap){
	  	foreach($cat_by_chap as $name_cat => $lines_by_cat){
	  		foreach($lines_by_cat as $type_elt => $lines_by_type){
	  			foreach($lines_by_type as $key => $_line){
	  			  $_line->getAdvancedPerms($is_praticien, $prescription->type, $mode_protocole, $mode_pharma, $operation_id);
	  			  if($_line->_class_name == "CPrescriptionLineElement"){
	  			    $_line->loadRefsPrises();
	  			  }
	  			}
	  		}
	  	}
	  }
  }    	
  // Calcul du nombre d'elements dans la prescription
	$prescription->countLinesMedsElements($praticien_sortie_id);
}

// Chargement des medicaments et commentaires de medicament
if ($full_mode || $chapitre == "medicament" || $mode_protocole || $mode_pharma) {
  if ($prescription->_id) {
	  if ($full_mode || $chapitre == "medicament" || $mode_protocole || $mode_pharma) {
			$prescription->loadRefsLinesMedComments();
		  foreach($prescription->_ref_lines_med_comments as $type => $lines_by_type){
		  	foreach($lines_by_type as $med_id => $_line_med){
		  	  if($_line_med->_class_name == "CPrescriptionLineMedicament"){
		  	    $_line_med->countBackRefs("administration");
		  	    $_line_med->loadRefsSubstitutionLines();
		  	    if($_line_med->_guid == $full_line_guid && $prescription->object_id){
		  	     $_prat_id = !$prescription->object_id ? $prescription->praticien_id : null;
		  	     $_line_med->loadMostUsedPoso(null, $_prat_id);
		  	    }
		  	  }
			    $_line_med->getAdvancedPerms($is_praticien, $prescription->type, $mode_protocole, $mode_pharma, $operation_id);
			    $_line_med->loadRefParentLine();
		  	}
		  }
	  }
	  
	  // Pour une prescription non protocole
	  if ($prescription->object_id) {
	    $object =& $prescription->_ref_object;
			// Chargement du patient
		  $object->loadRefPatient();
			$patient =& $object->_ref_patient;
			$patient->loadRefDossierMedical();
		  
			$object->loadRefsPrescriptions();
			
			// Chargement du dossier medicam
		  $dossier_medical =& $patient->_ref_dossier_medical;
		  $dossier_medical->updateFormFields();
		  $dossier_medical->loadRefsAntecedents();
		  $dossier_medical->loadRefsTraitements();
		  $dossier_medical->countAntecedents();
		  
		  // Calcul des alertes de la prescription
		  $allergies    = new CBcbControleAllergie();
		  $allergies->setPatient($patient);
		  $profil       = new CBcbControleProfil();
		  $profil->setPatient($patient);
	  }

	  $interactions = new CBcbControleInteraction();
		$IPC          = new CBcbControleIPC();
	  $surdosage    = new CBcbControleSurdosage();
	  $surdosage->setPrescription($prescription);
	  
	  $lines = array();
	  $lines["prescription"] = $prescription->_ref_prescription_lines;
	  
	  foreach($prescription->_ref_perfusions as $_perfusion){
	    foreach($_perfusion->_ref_lines as $_perf_line){
	      if($prescription->object_id){
	        $allergies->addProduit($_perf_line->code_cip);
	        $profil->addProduit($_perf_line->code_cip);
	      }			    
		    $interactions->addProduit($_perf_line->code_cip);
		    $IPC->addProduit($_perf_line->code_cip);
	    }
	  }
	  foreach($lines as $type => $type_line){
		  foreach($type_line as &$line) {
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
	  }		  
	  $alertesInteractions = $interactions->getInteractions();
	  $alertesIPC          = $IPC->getIPC();
	  $alertesPosologie    = $surdosage->getSurdosage();
	  
	  if(!$prescription->object_id){
	    $prescription->_alertes["allergie"] = array();
	    $prescription->_alertes["profil"] = array(); 
	  }
	  
	  $prescription->_scores["hors_livret"] = 0;
	  foreach($lines as $type_line){
	    foreach($type_line as &$line) {
	      if($prescription->object_id){
	        $prescription->checkAllergies($alertesAllergies, $line->code_cip);
	        $prescription->checkProfil($alertesProfil, $line->code_cip);
	      }		      
	      $prescription->checkIPC($alertesIPC, $line->code_cip);
	      $prescription->checkInteractions($alertesInteractions, $line->code_cip);
	      $prescription->checkPoso($alertesPosologie, $line->code_cip);

	      if(!$line->_ref_produit->inLivret && $prescription->type == "sejour"){
	        $prescription->_scores["hors_livret"]++;
	      }
	    }
	  }
	  foreach($prescription->_ref_perfusions as $_perfusion){
	  	foreach($_perfusion->_ref_lines as $_perf_line){
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

		  
			// Chargement du poids du patient
			$patient->loadRefConstantesMedicales();
		  $constantes_medicales = $patient->_ref_constantes_medicales;
		  $poids = $constantes_medicales->poids;
		
		  if($object->_class_name == "CSejour"){
		    $whereOp = array();
		    $whereOp["annulee"] = " = '0'";
		    $object->loadRefsOperations($whereOp);
		    foreach($object->_ref_operations as $_operation){
		      $_operation->loadRefPlageOp();
		      $prescription->_dates_dispo[$_operation->_id] = $_operation->_datetime;
		    }
		  }
		}
	}
}

// Chargement des fovoris 
if($prescription->_id){
  if($pratSel_id){
    $prescription->_current_praticien_id = $pratSel_id;
  }
	if($prescription->object_id && $prescription->_current_praticien_id){
    $listFavoris = CPrescription::getFavorisPraticien($prescription->_current_praticien_id);
	} else {
	  // Dans le cas d'un protocole
    // Si le protocole appartient à un praticien, on charge les favoris du praticien
	  if($prescription->praticien_id){
      $listFavoris = CPrescription::getFavorisPraticien($prescription->praticien_id);
	  } else {
	  // Sinon, on charge les favoris du user courant si c'est un praticien (protocole de cabinet et d'etablissement)
	    if($is_praticien){
	      $listFavoris = CPrescription::getFavorisPraticien($AppUI->user_id);
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
$protocole_line->debut = mbDate();

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
$filter_line->debut = mbDate();

$prise = new CPrisePosologie();
$prise->quantite = 1.0;

// Chargement des aides
$prescriptionLineMedicament = new CPrescriptionLineMedicament();
$prescriptionLineElement = new CPrescriptionLineElement();
$aides_prescription = array();
if($prescription->_id){
  // Si protocole
  if(!$prescription->object_id){
    $prescription->_praticiens = array();
    $prescription->_praticiens[$AppUI->user_id] = "";
  }
  if($prescription->_praticiens){
		foreach($prescription->_praticiens as $praticien_id => $praticien->_view){
		  $prescriptionLineMedicament->loadAides($praticien_id);
		  $aides_prescription[$praticien_id]["CPrescriptionLineMedicament"] = $prescriptionLineMedicament->_aides["commentaire"]["no_enum"];
		  $prescriptionLineElement->loadAides($praticien_id);
		  $aides_prescription[$praticien_id]["CPrescriptionLineElement"] = $prescriptionLineElement->_aides["commentaire"]["no_enum"];
		}
  }
}


$_sejour = new CSejour();
$_sejour->load($sejour_id);

$operation = new COperation();
if($operation->load($operation_id)) {
 $operation->loadRefPlageOp();
 $operation->_ref_anesth->loadRefFunction();
}

$_chir_id   = $chir_id   ? $chir_id : ($AppUI->_ref_user->isPraticien() ? $AppUI->user_id : $_sejour->praticien_id);
$_anesth_id = $anesth_id ? $anesth_id : ($AppUI->_ref_user->isFromType(array("Anesthésiste")) ? 
                                            $AppUI->user_id : 
                                            ($operation->_id ? $operation->_ref_plageop->anesth_id : null));
                                            
if(isset($operation->_ref_anesth->_id)){
  unset($listPrats[$operation->_ref_anesth->_id]);
}
if(isset($prescription->_ref_current_praticien->_id)){
  unset($listPrats[$prescription->_ref_current_praticien->_id]);
}


// Création du template
$smarty = new CSmartyDP();

// Mode permettant de supprimer qq elements de la ligne en salle d'op (Anesthesie)
$smarty->assign("mode_induction_perop", false);

$smarty->assign("aides_prescription", $aides_prescription);
$smarty->assign("full_line_guid", $full_line_guid);
$smarty->assign("mode_anesth", $mode_anesth);
$smarty->assign("historique", $historique);
$smarty->assign("filter_line", $filter_line);
$smarty->assign("hours", $hours);
$smarty->assign("mins", $mins);
$smarty->assign("praticien_sortie_id", $praticien_sortie_id);
$smarty->assign("contexteType"       , $contexteType);
$smarty->assign("httpreq"            , 1);
$smarty->assign("sejour_id"          , $sejour_id);
$smarty->assign("is_praticien"       , $is_praticien);
$smarty->assign("today"              , mbDate());
$smarty->assign("now"                , mbDateTime());
$smarty->assign("poids"              , $poids);
$smarty->assign("categories"         , $categories);
$smarty->assign("executants"         , $executants);
$smarty->assign("moments"            , $moments);
$smarty->assign("prise_posologie"    , $prise);
$smarty->assign("protocole"          , new CPrescription());
$smarty->assign("alertesAllergies"   , $alertesAllergies);
$smarty->assign("alertesInteractions", $alertesInteractions);
$smarty->assign("alertesIPC"         , $alertesIPC);
$smarty->assign("alertesProfil"      , $alertesProfil);
$smarty->assign("prescription"       , $prescription);
$smarty->assign("listPrats"          , $listPrats);
$smarty->assign("listFavoris"        , $listFavoris);
$smarty->assign("category"           , $chapitre);
$smarty->assign("categories"         , $categories);
$smarty->assign("class_category"     , new CCategoryPrescription());
$smarty->assign("refresh_pharma"     , $refresh_pharma);
$smarty->assign("mode_pharma"        , $mode_pharma);
$smarty->assign("full_mode"          , $full_mode);
$smarty->assign("protocole_line"     , $protocole_line);
$smarty->assign("mode_protocole"     , $mode_protocole);
$smarty->assign("prescriptions_sejour", $prescriptions_sejour);
$smarty->assign("dossier_medical"    , $dossier_medical);
$smarty->assign("now_time", mbTime());
$smarty->assign("mode_pack", "0");
$smarty->assign("readonly",            $readonly && $prescription->object_id);
$smarty->assign("lite", $lite);
$smarty->assign("perfusion", new CPerfusion());
$smarty->assign("operation_id", $operation_id);
$smarty->assign("pratSel_id", $pratSel_id);
$smarty->assign("mode_sejour", $mode_sejour);
  
if($full_mode){
  $smarty->assign("praticien_sejour", $_sejour->praticien_id);
  $smarty->assign("chir_id", $_chir_id);
  $smarty->assign("anesth_id", $_anesth_id);
  $smarty->assign("operation", $operation);
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
      if($chapitre == "medicament"){
       	$smarty->display("inc_div_medicament.tpl");
      } 
      // refresh Element
      else {
        $smarty->assign("element", $chapitre);
        if (CAppUI::conf("dmi CDMI active") && CModule::getActive('dmi') && $chapitre == 'dmi') {
        	$smarty->display("../../dmi/templates/inc_div_dmi.tpl");
        }
        else {
          $smarty->display("inc_div_element.tpl");
        }
      }
    }
  }
}


?>