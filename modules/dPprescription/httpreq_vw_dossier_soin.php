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
$nb_decalage  = CValue::get("nb_decalage", 2);
$line_type    = CValue::get("line_type", "service");  // Bloc en salle d'op / service en hospi
$mode_dossier = CValue::get("mode_dossier", "administration");
$chapitre     = CValue::get("chapitre"); // Chapitre a rafraichir
$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");
$unite_prise  = CValue::get("unite_prise");

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

$config_service = new CConfigService();
$configs = $config_service->getConfigForService($service_id);

$matin = range($configs["Borne matin min"], $configs["Borne matin max"]);
$soir = range($configs["Borne soir min"], $configs["Borne soir max"]);
$nuit_soir = range($configs["Borne nuit min"], 23);
$nuit_matin = range(00, $configs["Borne nuit max"]);

foreach($matin as &$_hour_matin){
  $_hour_matin = str_pad($_hour_matin, 2, "0", STR_PAD_LEFT);  
}
foreach($soir as &$_soir_matin){
  $_soir_matin = str_pad($_soir_matin, 2, "0", STR_PAD_LEFT);  
}
foreach($nuit_soir as &$_hour_nuit_soir){
  $nuit[] = str_pad($_hour_nuit_soir, 2, "0", STR_PAD_LEFT);
}
foreach($nuit_matin as &$_hour_nuit_matin){
  $nuit[] = str_pad($_hour_nuit_matin, 2, "0", STR_PAD_LEFT);
}

// Recuperation de l'heure courante
$time = mbTransformTime(null,null,"%H");

// Construction de la structure de date à parcourir dans le tpl
if(in_array($time, $matin)){
  $dates = array(mbDate("- 1 DAY", $date) => array("soir" => $soir, "nuit" => $nuit), 
                 $date                    => array("matin" => $matin, "soir" => $soir, "nuit" => $nuit));
}
if(in_array($time, $soir)){
  $dates = array(mbDate("- 1 DAY", $date) => array("nuit" => $nuit),
                 $date                    => array("matin" => $matin, "soir" => $soir, "nuit" => $nuit),
                 mbDate("+ 1 DAY", $date) => array("matin" => $matin));
}
if(in_array($time, $nuit)){
  $dates = array($date                    => array("matin" => $matin, "soir" => $soir, "nuit" => $nuit), 
                 mbDate("+ 1 DAY", $date) => array("matin" => $matin, "soir" => $soir));
}

$composition_dossier = array();
foreach($dates as $curr_date => $_date){
  foreach($_date as $moment_journee => $_hours){
    $composition_dossier[] = "$curr_date-$moment_journee";
    foreach($_hours as $_hour){
      $date_reelle = $curr_date;
      if($moment_journee == "nuit" && $_hour < "12:00:00"){
        $date_reelle = mbDate("+ 1 DAY", $curr_date);
      }
      $_dates[$date_reelle] = $date_reelle;
      $tabHours[$curr_date][$moment_journee][$date_reelle]["$_hour:00:00"] = $_hour;
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
			  $line->calculPrises($prescription, $curr_date, null, null, true);
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
        $line->calculPrises($prescription, $curr_date, $name_chap, $name_cat, true);
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
      $line->calculPrisesPrevues($curr_date);
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
		  $prescription->loadRefsLinesMedByCat("1","1",$line_type);
      foreach($prescription->_ref_prescription_lines as &$_line_med){
			  $_line_med->loadRefLogSignee();
			  $_line_med->countSubstitutionsLines();
			  $_line_med->countBackRefs("administration");
				$_line_med->loadRefsSubstitutionLines();
				$_line_med->loadRefProduitPrescription();
			}
    } elseif($chapitre == "perf") {
      // Chargement des prescription_line_mixes
	    $prescription->loadRefsPrescriptionLineMixes("1", $line_type);
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
    } elseif (!$chapitre) {
      // Parcours initial pour afficher les onglets utiles (pas de chapitre de specifié)
      $prescription->loadRefsPrescriptionLineMixes("1", $line_type);
      $prescription->loadRefsLinesMedByCat("1","1",$line_type);
	    
      // Chargement des lignes d'elements 
		  $prescription->loadRefsLinesElementByCat("1",null,$line_type);
			
		  // Calcul des modifications recentes par chapitre
		  $prescription->countRecentModif();
		  $prescription->countUrgence($date);
    } else {
      // Chargement des lignes d'elements  avec pour chapitre $chapitre
		  $prescription->loadRefsLinesElementByCat("1",$chapitre,$line_type);
    }
		
    $with_calcul = $chapitre ? true : false; 
		$prescription->calculPlanSoin($_dates, 0, null, null, null, $with_calcul);

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
$smarty->assign("count_matin"         , count($matin)+2);
$smarty->assign("count_soir"          , count($soir)+2);
$smarty->assign("count_nuit"          , count($nuit)+2);
$smarty->assign("composition_dossier" , $composition_dossier);
$smarty->assign("prev_date"           , mbDate("- 1 DAY", $date));
$smarty->assign("next_date"           , mbDate("+ 1 DAY", $date));
$smarty->assign("today"               , mbDate());
$smarty->assign("move_dossier_soin"   , false);

if($object_id && $object_class){
  // Affichage d'une ligne
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
} else {
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