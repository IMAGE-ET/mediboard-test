<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $can;
$can->needsRead();

$sejour_id    = mbGetValueFromGetOrSession("sejour_id");
$date         = mbGetValueFromGetOrSession("date");
$nb_decalage  = mbGetValueFromGet("nb_decalage", 2);
$line_type    = mbGetValueFromGet("line_type", "service");  // Bloc en salle d'op / service en hospi
$mode_bloc    = mbGetValueFromGet("mode_bloc", 0);
$now          = mbDateTime();
$mode_dossier = mbGetValueFromGet("mode_dossier", "administration");

$object_id = mbGetValueFromGet("object_id");
$object_class = mbGetValueFromGet("object_class");
$unite_prise = mbGetValueFromGet("unite_prise");


// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->loadRefPraticien();

// Chargement du poids et de la chambre du patient
$patient =& $sejour->_ref_patient;
$patient->loadRefConstantesMedicales();
$const_med = $patient->_ref_constantes_medicales;
$poids = $const_med->poids;

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->object_id = $sejour_id;
$prescription->object_class = "CSejour";
$prescription->type = "sejour";
$prescription->loadMatchingObject();
$prescription_id = $prescription->_id;

// Chargement des categories pour chaque chapitre
$categories = CCategoryPrescription::loadCategoriesByChap();

$operation = new COperation();
$operations = array();

$matin = range(CAppUI::conf("dPprescription CPrisePosologie heures matin min"), CAppUI::conf("dPprescription CPrisePosologie heures matin max"));
$soir = range(CAppUI::conf("dPprescription CPrisePosologie heures soir min"), CAppUI::conf("dPprescription CPrisePosologie heures soir max"));
$nuit_soir = range(CAppUI::conf("dPprescription CPrisePosologie heures nuit min"), 23);
$nuit_matin = range(00, CAppUI::conf("dPprescription CPrisePosologie heures nuit max"));

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

$count_matin = count($matin) + 2;
$count_soir = count($soir) + 2;
$count_nuit = count($nuit) + 2;


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
if($object_id && $object_class && $unite_prise){
  $line = new $object_class;
  $line->load($object_id);
  
  if($line_type != "service"){
    $_dates = array();
    $_dates[] = $date;
  }
  foreach($_dates as $curr_date){
    // Refresh d'une ligne de medicament
    if($line->_class_name == "CPrescriptionLineMedicament"){
       if(!$line->_fin_reelle){
		    $line->_fin_reelle = $prescription->_ref_object->_sortie;
		  }
		  if(($curr_date >= $line->debut && $curr_date <= mbDate($line->_fin_reelle))){     	
				$line->calculAdministrations($curr_date);
        $line->_ref_produit->loadClasseATC();
        $line->_ref_produit->loadRefsFichesATC();
				$line->calculPrises($prescription, $curr_date);
		  }
    } 
    // refresh d'une ligne d'element
    else {
      $element = $line->_ref_element_prescription;
    	$name_cat = $element->category_prescription_id;
      $element->loadRefCategory();
      $name_chap = $element->_ref_category_prescription->chapitre;
       if(($curr_date >= $line->debut && $curr_date <= mbDate($line->_fin_reelle))){
     	  $line->calculAdministrations($curr_date);
		    $line->calculPrises($prescription, $curr_date, 0, $name_chap, $name_cat);
      }
    }
    // Suppression des prises replanifiées
		$line->removePrisesPlanif();
  }
} 


// Calcul du dossier de soin complet
else {
	if($prescription->_id){
		// Chargement des lignes
		$prescription->loadRefsLinesMedByCat("1","1",$line_type);
		$prescription->loadRefsLinesElementByCat("1","",$line_type);
		$prescription->_ref_object->loadRefPrescriptionTraitement();	 
		$traitement_personnel = $prescription->_ref_object->_ref_prescription_traitement;
		if($traitement_personnel->_id){
		  $traitement_personnel->loadRefsLinesMedByCat("1","1",$line_type);
		}
		  	  
		// Chargement des perfusions
	  $prescription->loadRefsPerfusions();
	  foreach($prescription->_ref_perfusions as &$_perfusion){
	    $_perfusion->loadRefsLines();
	  }
	
		foreach($prescription->_ref_prescription_lines as &$_line_med){
		  if(!$_line_med->countBackRefs("administration")){
			  if(!$_line_med->substitute_for){
			    $_line_med->loadRefsSubstitutionLines();   
			  } else {
			    $_base_line = new CPrescriptionLineMedicament();
			    $_base_line->load($_line_med->substitute_for);
			    $_base_line->loadRefsSubstitutionLines();
			    $_line_med->_ref_substitution_lines = $_base_line->_ref_substitution_lines;
			    // Ajout de la ligne d'origine dans le tableau
			    $_line_med->_ref_substitution_lines[$_base_line->_id] = $_base_line;
			    // Suppression de la ligne actuelle
			    unset($_line_med->_ref_substitution_lines[$_line_med->_id]);
			  }
		  }
		}
	
		// REF: Passer directement une date min et une date max au calculPlanSoin
	  if($line_type == "service"){
		  foreach($_dates as $curr_date){
		    $prescription->calculPlanSoin($curr_date, 0);
		  }
	  } else {
	    $prescription->calculPlanSoin($date, 0);
	  }
	  
	  // Chargement des operations
	  if($prescription->_ref_object->_class_name == "CSejour"){
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
	
	// Calcul du rowspan pour les medicaments
	$types = array("med","inj");
	foreach($types as $_type_med){
	  $produits = ($_type_med == "med") ? $prescription->_ref_lines_med_for_plan : $prescription->_ref_injections_for_plan;
		if($produits){
		  foreach($produits as $_code_ATC => $_cat_ATC){
			  if(!isset($prescription->_nb_produit_by_cat[$_code_ATC])){
			    $prescription->_nb_produit_by_cat[$_type_med][$_code_ATC] = 0;
			  }
			  foreach($_cat_ATC as $line_id => $_line) {
			    foreach($_line as $unite_prise => $line_med){
			      if(!isset($prescription->_nb_produit_by_chap[$_type_med])){
						  $prescription->_nb_produit_by_chap[$_type_med] = 0;
						}
						$prescription->_nb_produit_by_chap[$_type_med]++;
			      $prescription->_nb_produit_by_cat[$_type_med][$_code_ATC]++;
			    }
			  }
			}
		}
	}
	
	// Calcul du rowspan pour les elements
	if($prescription->_ref_lines_elt_for_plan){
		foreach($prescription->_ref_lines_elt_for_plan as $name_chap => $elements_chap){
		  foreach($elements_chap as $name_cat => $elements_cat){
		    if(!isset($prescription->_nb_produit_by_cat[$name_cat])){
		      $prescription->_nb_produit_by_cat[$name_cat] = 0;
		    }
		    foreach($elements_cat as $_element){
		      foreach($_element as $element){
		        if(!isset($prescription->_nb_produit_by_chap[$name_chap])){
					    $prescription->_nb_produit_by_chap[$name_chap] = 0;  
					  }
					  $prescription->_nb_produit_by_chap[$name_chap]++;
		        $prescription->_nb_produit_by_cat[$name_cat]++;
		      }
		    }
		  }
		}     
	}
	
	$transmission = new CTransmissionMedicale();
	$where = array();
	$where[] = "(object_class = 'CCategoryPrescription') OR 
	            (object_class = 'CPrescriptionLineElement') OR 
	            (object_class = 'CPrescriptionLineMedicament') OR 
							(object_class = 'CPerfusion')";
	$where["sejour_id"] = " = '$sejour->_id'";
	$transmissions_by_class = $transmission->loadList($where);
	
	foreach($transmissions_by_class as $_transmission){
	  $_transmission->loadRefsFwd();
		$prescription->_transmissions[$_transmission->object_class][$_transmission->object_id][$_transmission->_id] = $_transmission;
	}
}


$signe_decalage = ($nb_decalage < 0) ? "-" : "+";

$real_date = mbDate();
$real_time = mbTime();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("signe_decalage"      , $signe_decalage);
$smarty->assign("nb_decalage"         , abs($nb_decalage));
$smarty->assign("poids"               , $poids);
$smarty->assign("patient"             , $patient);
$smarty->assign("prescription"        , $prescription);
$smarty->assign("tabHours"            , $tabHours);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("prescription_id"     , $prescription_id);
$smarty->assign("date"                , $date);
$smarty->assign("now"                 , $now);
$smarty->assign("categories"          , $categories);
$smarty->assign("real_date"           , $real_date);
$smarty->assign("real_time"           , $real_time);
$smarty->assign("categorie"           , new CCategoryPrescription());
$smarty->assign("mode_bloc"           , $mode_bloc);
$smarty->assign("operations"          , $operations);
$smarty->assign("mode_dossier"        , $mode_dossier);
$smarty->assign("count_matin"         , $count_matin);
$smarty->assign("count_soir"          , $count_soir);
$smarty->assign("count_nuit"          , $count_nuit);
$smarty->assign("composition_dossier" , $composition_dossier);

$smarty->assign("prev_date", mbDate("- 1 DAY", $date));
$smarty->assign("next_date", mbDate("+ 1 DAY", $date));
$smarty->assign("today", mbDate());

// Refresh de seulement 1 ligne du plan de soin

if($object_id && $object_class && $unite_prise){
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
  $smarty->assign("move_dossier_soin", true);
  $smarty->assign("nodebug", true);
  
  $smarty->display("inc_vw_content_line_dossier_soin.tpl");
} else {
  // Refresh du plan de soin complet
  $smarty->assign("move_dossier_soin"   , false);
  $smarty->display("inc_vw_dossier_soins.tpl");
}

?>