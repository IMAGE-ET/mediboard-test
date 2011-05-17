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

$sejour_id    = CValue::getOrSession("sejour_id");
$date         = CValue::getOrSession("date");
$nb_decalage  = CValue::get("nb_decalage");
$mode_dossier = CValue::get("mode_dossier", "administration");
$chapitre     = CValue::get("chapitre"); // Chapitre a rafraichir
$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");
$unite_prise  = CValue::get("unite_prise");
$without_check_date = CValue::get("without_check_date", "0");
$hide_close  = CValue::get("hide_close", 0);

// Permet de gerer le cas ou des unites de prises contiennent des '
$unite_prise = stripslashes(preg_replace('~&#0*([0-9]+);~e', 'chr(\\1)', $unite_prise));

// Recuperation du sejour_id si seulement l'object est passé
if($object_id && $object_class){
	$object = new $object_class;
	$object->load($object_id);
	$sejour_id = $object->_ref_prescription->object_id;
}

// Initialisations
$operation = new COperation();
$operations = array();

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->loadRefPraticien();

// Chargement des caracteristiques du patient
$patient =& $sejour->_ref_patient;
$patient->loadRefPhotoIdentite();
$patient->loadRefConstantesMedicales();
$patient->loadRefDossierMedical();
$dossier_medical = $patient->_ref_dossier_medical;

if($dossier_medical->_id){
  $dossier_medical->loadRefsAllergies();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAntecedents();
  $dossier_medical->countAllergies();
}

$const_med = $patient->_ref_constantes_medicales;
$poids = $const_med->poids;

// Chargement de la prescription à partir du sejour
$prescription = new CPrescription();
$prescription->object_id = $sejour_id;
$prescription->object_class = "CSejour";
$prescription->type = "sejour";
$prescription->loadMatchingObject();

// Chargement de toutes les planifs systemes si celles-ci ne sont pas deja chargées
$prescription->calculAllPlanifSysteme();

// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

// Chargement des configs de service
$sejour->loadRefCurrAffectation($date);

if($sejour->_ref_curr_affectation->_id){
  $service_id = $sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->service_id;
} else {
  $service_id = "none";
}

$configs = CConfigService::getAllFor($service_id);

if(!$nb_decalage){
	$nb_decalage = $configs["Nombre postes avant"];
}

if(!$without_check_date && !($object_id && $object_class) && !$chapitre){
	// Si la date actuelle est inférieure a l'heure affichée sur le plan de soins, on affiche le plan de soins de la veille (cas de la nuit)
	$datetime_limit = mbDateTime($configs["Poste 1"].":00:00");	
	if(mbDateTime() < $datetime_limit){
	  $date = mbDate("- 1 DAY");
	} else {
	  $date = mbDate();
	}
}

$composition_dossier = array();
$bornes_composition_dossier = array();
$count_composition_dossier = array();

$tabHours = CAdministration::getTimingPlanSoins($date, $configs);
foreach($tabHours as $_key_date => $_period_date){
  foreach($_period_date as $_key_periode => $_period_dates){
		$count_composition_dossier[$_key_date][$_key_periode] = CAppUI::conf("dPprescription CPrescription manual_planif") ? 3 : 2;
		$first_date = reset(array_keys($_period_dates));
    $first_time = reset(reset($_period_dates));
    $last_date = end(array_keys($_period_dates));
    $last_time = end(end($_period_dates));
		
    $composition_dossier[] = "$_key_date-$_key_periode";
		
    $bornes_composition_dossier["$_key_date-$_key_periode"]["min"] = "$first_date $first_time:00:00";
    $bornes_composition_dossier["$_key_date-$_key_periode"]["max"] = "$last_date $last_time:00:00";
		
    
		foreach($_period_dates as $_key_real_date => $_period_hours){
			$count_composition_dossier[$_key_date][$_key_periode] += count($_period_hours);
      $_dates[$_key_real_date] = $_key_real_date;
    }
  }
}

// Calcul du dossier de soin pour une ligne
if($object_id && $object_class){
  // Chargement de la ligne de prescription
  $line = new $object_class;
  $line->load($object_id);
	
  if($line instanceof CPrescriptionLineMedicament){
  	$line->countSubstitutionsLines();
	  $line->countBackRefs("administration");
		$line->loadRefsSubstitutionLines();
  }

  foreach($_dates as $curr_date){
    // Refresh d'une ligne de medicament
    if($line instanceof CPrescriptionLineMedicament){
       if(!$line->_fin_reelle){
		    $line->_fin_reelle = $prescription->_ref_object->_sortie;
		  }
		  $line->calculAdministrations($curr_date);
      $line->_ref_produit->loadClasseATC();
      $line->_ref_produit->loadRefsFichesATC();
      if(($curr_date >= $line->debut && $curr_date <= mbDate($line->_fin_reelle))){     
			  $line->calculPrises($prescription, $curr_date, null, null, true, CAppUI::conf("dPprescription CPrescription manual_planif"));
      }
		  $line->removePrisesPlanif();
    }
		 
    // refresh d'une ligne d'element
    if($line instanceof CPrescriptionLineElement) {
      $element = $line->_ref_element_prescription;
    	$name_cat = $element->category_prescription_id;
      $element->loadRefCategory();
      $name_chap = $element->_ref_category_prescription->chapitre;
     	$line->calculAdministrations($curr_date);  
  	  if(($curr_date >= $line->debut && $curr_date <= mbDate($line->_fin_reelle))){
        $line->calculPrises($prescription, $curr_date, $name_chap, $name_cat, true, CAppUI::conf("dPprescription CPrescription manual_planif"));
  	  }
		  $line->removePrisesPlanif();
    }
  }
    
  if($line instanceof CPrescriptionLineMix){
	 	$line->countSubstitutionsLines();
		$line->loadRefsSubstitutionLines();
    $line->loadRefsLines();
		$line->loadVoies();
    $line->loadRefPraticien();
	  $line->loadRefLogSignaturePrat();
		$line->calculVariations();
		
		// Calcul des prises prevues
		$line->calculQuantiteTotal();
		foreach($_dates as $curr_date){
      $line->calculPrisesPrevues($curr_date, CAppUI::conf("dPprescription CPrescription manual_planif"));
		}
    $line->calculAdministrations();
    
		// Chargement des transmissions de la prescription_line_mix
  	$transmission = new CTransmissionMedicale();
  	$transmission->object_class = "CPrescriptionLineMix";
  	$transmission->object_id = $line->_id;
  	$transmission->sejour_id = $sejour->_id;
	  $transmissions = $transmission->loadMatchingList();
	  
	  foreach($transmissions as $_transmission){
	    $_transmission->loadRefsFwd();
	    if($_transmission->object_id && $_transmission->object_class){
		    $prescription->_transmissions[$_transmission->object_class][$_transmission->object_id][$_transmission->_id] = $_transmission;
	    }
	  }
  }
} 


// Calcul du dossier de soin complet
else {
	if($prescription->_id){
		// Chargement des lignes de medicament
    if($chapitre == "med" || $chapitre == "inj"){
		  $prescription->loadRefsLinesMedByCat("1","1");
      foreach($prescription->_ref_prescription_lines as &$_line_med){
			  $_line_med->loadRefLogSignee();
			  $_line_med->countSubstitutionsLines();
			  $_line_med->countBackRefs("administration");
				$_line_med->loadRefsSubstitutionLines();
				$_line_med->loadRefProduitPrescription();
			}
    } elseif($chapitre == "perfusion" || $chapitre == "aerosol" || $chapitre == "alimentation" || $chapitre == "oxygene") {
      // Chargement des prescription_line_mixes
	    $prescription->loadRefsPrescriptionLineMixes($chapitre,"1");
		  foreach($prescription->_ref_prescription_line_mixes as &$_prescription_line_mix){
		    $_prescription_line_mix->countSubstitutionsLines();
		    $_prescription_line_mix->loadRefsSubstitutionLines();
		    $_prescription_line_mix->getRecentModification();
		    $_prescription_line_mix->loadRefsLines();
				$_prescription_line_mix->loadVoies();
		    $_prescription_line_mix->loadRefPraticien();
		    $_prescription_line_mix->loadRefLogSignaturePrat();
				$_prescription_line_mix->calculVariations();
      }
    } 
		elseif ($chapitre == "inscription"){
			// Chargement des inscriptions effectuées
			$prescription->loadRefsLinesInscriptions();
			foreach($prescription->_ref_lines_inscriptions as $_inscriptions_by_type){
				foreach($_inscriptions_by_type as $_inscription){
          $_inscription->countBackRefs("administration");
				}
			}
		}
		elseif (!$chapitre) {
      // Parcours initial pour afficher les onglets utiles (pas de chapitre de specifié)
      $prescription->loadRefsPrescriptionLineMixes("","1");
      $prescription->loadRefsLinesMedByCat("1","1");
	    
      // Chargement des lignes d'elements
		  $prescription->loadRefsLinesElementByCat("1",null);
			
			if(@!CAppUI::conf("object_handlers CPrescriptionAlerteHandler")){
		    // Calcul des modifications recentes par chapitre
		    $prescription->countRecentModif();
		    $prescription->countUrgence($date);
			}
		
		} else {
      // Chargement des lignes d'elements  avec pour chapitre $chapitre
		  $prescription->loadRefsLinesElementByCat("1",$chapitre);
    }
		
    $with_calcul = $chapitre ? true : false; 
		$prescription->calculPlanSoin($_dates, 0, null, null, null, $with_calcul, "");

	  // Chargement des operations
	  if($prescription->_ref_object instanceof CSejour){
	    $operation = new COperation();
	    $operation->sejour_id = $prescription->object_id;
	    $operation->annulee = "0";
	    $_operations  = $operation->loadMatchingList();
	    foreach($_operations as $_operation){
	      if($_operation->time_operation != "00:00:00"){
	        $_operation->loadRefPlageOp(); 
	        $hour_operation = mbTransformTime(null, $_operation->time_operation, '%H');
	        $hour_operation = (($hour_operation % 2) == 0) ? $hour_operation : $hour_operation-1;
	        $hour_operation .= ":00:00";
	        $operations["{$_operation->_ref_plageop->date} $hour_operation"] = $_operation->time_operation;
	      }
	    }
	  }	 
	}
	// Calcul du nombre de produits (rowspan)
	$prescription->calculNbProduit();
	// Chargement des transmissions qui ciblent les lignes de la prescription
	$prescription->loadAllTransmissions();
}
$signe_decalage = ($nb_decalage < 0) ? "-" : "+";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("signe_decalage"      , $signe_decalage);
$smarty->assign("nb_decalage"         , abs($nb_decalage));
$smarty->assign("poids"               , $poids);
$smarty->assign("patient"             , $patient);
$smarty->assign("prescription"        , $prescription);
$smarty->assign("tabHours"            , $tabHours);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("prescription_id"     , $prescription->_id);
$smarty->assign("date"                , $date);
$smarty->assign("now"                 , mbDateTime());
$smarty->assign("categories"          , $categories);
$smarty->assign("real_date"           , mbDate());
$smarty->assign("real_time"           , mbTime());
$smarty->assign("categorie"           , new CCategoryPrescription());
$smarty->assign("operations"          , $operations);
$smarty->assign("mode_dossier"        , $mode_dossier);
$smarty->assign("count_composition_dossier", $count_composition_dossier);
$smarty->assign("composition_dossier" , $composition_dossier);
$smarty->assign("bornes_composition_dossier", $bornes_composition_dossier);
$smarty->assign("prev_date"           , mbDate("- 1 DAY", $date));
$smarty->assign("next_date"           , mbDate("+ 1 DAY", $date));
$smarty->assign("today"               , mbDate());
$smarty->assign("move_dossier_soin"   , false);
$smarty->assign("configs"             , $configs);
$smarty->assign("params"              , CConstantesMedicales::$list_constantes);
$smarty->assign("hide_close"          , $hide_close);

// Affichage d'une ligne
if($object_id && $object_class){
  $smarty->assign("move_dossier_soin", true);
  $smarty->assign("nodebug", true);	 
  if($line->_class_name == "CPrescriptionLineMix"){
    $smarty->assign("_prescription_line_mix", $line);
    $smarty->display("inc_vw_perf_dossier_soin.tpl");
  } else {
	  if($line->_class_name == "CPrescriptionLineElement"){
	    $smarty->assign("name_cat", $name_cat);
	    $smarty->assign("name_chap", $name_chap);  
	  }
	  $smarty->assign("line", $line);
	  $smarty->assign("line_id", $line->_id);
	  $smarty->assign("line_class", $line->_class_name);
	  $smarty->assign("transmissions_line", $line->_transmissions);
	  $smarty->assign("administrations_line", $line->_administrations);
		$smarty->assign("unite_prise", $unite_prise);
		
	  $smarty->display("inc_vw_content_line_dossier_soin.tpl");
  }
} 
else {
  // Affichage d'un chapitre
  if($chapitre){
	  $smarty->assign("move_dossier_soin", false);
    $smarty->assign("chapitre", $chapitre);
    $smarty->assign("nodebug", true);	 
    $smarty->display("inc_chapitre_dossier_soin.tpl");
  } 
  // Affichage du plan de soin complet
  else {
	  $smarty->assign("move_dossier_soin"   , false);
	  $smarty->display("inc_vw_dossier_soins.tpl");
  }
}

?>