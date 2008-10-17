<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$sejour_id   = mbGetValueFromGetOrSession("sejour_id");
$date        = mbGetValueFromGetOrSession("date");
$nb_decalage = mbGetValueFromGetOrSession("nb_decalage",0);
$now         = mbDateTime();

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
$dates = array(mbDate("- 1 DAY", $date), $date, mbDate("+ 1 DAY", $date));

$types = array("med", "elt");
foreach($types as $type){
  $prescription->_lines[$type] = array();
  $prescription->_intitule_prise[$type] = array();
}
 
$hours_deb = "02|04|06|08|10|12";
$hours_fin = "14|16|18|20|22|24";
$hours = $hours_deb."|".$hours_fin;

$hier = mbDate("- 1 DAY", $date);
$demain = mbDate("+ 1 DAY", $date);

$hours = explode("|",$hours);
$hours_deb = explode("|",$hours_deb);
$hours_fin = explode("|",$hours_fin);


foreach($hours_fin as $_hour_fin){
  $tabHours[$hier]["$_hour_fin:00:00"] = $_hour_fin;
}
foreach($hours as $_hour){
  $tabHours[$date]["$_hour:00:00"] = $_hour;
}
foreach($hours_deb as $_hour_deb){
  $tabHours[$demain]["$_hour_deb:00:00"] = $_hour_deb;
}

// Calcul permettant de regrouper toutes les heures dans un tableau afin d'afficher les medicaments
// dont les heures ne sont pas spécifié dans le tableau
$heures = array();
$list_hours = range(1,24);
$last_hour_in_array = reset($tabHours[$date]); 
foreach($list_hours as &$hour){
  $hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
  if(in_array($hour, $tabHours[$date])){
    $last_hour_in_array = $hour;
  }
  $heures[$hour] = $last_hour_in_array;
}

if($prescription->_id){
	// Chargement des lignes
	$prescription->loadRefsLinesMedByCat("1","1","service");
	
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
	
	$prescription->loadRefsLinesElementByCat("1","","service");
	$prescription->_ref_object->loadRefPrescriptionTraitement();
		 
	$traitement_personnel = $prescription->_ref_object->_ref_prescription_traitement;
	if($traitement_personnel->_id){
	  $traitement_personnel->loadRefsLinesMedByCat("1","1");
	}
	  	  
	// Calcul du plan de soin pour la journée $date
  foreach($dates as $_date){
    $prescription->calculPlanSoin($_date, 0, $heures);
  }
}

// Calcul du rowspan pour les medicaments
if($prescription->_ref_lines_med_for_plan){
	foreach($prescription->_ref_lines_med_for_plan as $_code_ATC => $_cat_ATC){
	  if(!isset($prescription->_nb_produit_by_cat[$_code_ATC])){
	    $prescription->_nb_produit_by_cat[$_code_ATC] = 0;
	  }
	  foreach($_cat_ATC as $_line) {
	    foreach($_line as $line_med){
	      $prescription->_nb_produit_by_cat[$_code_ATC]++;
	    }
	  }
	}
}

// Calcul du rowspan pour les elements
if($prescription->_ref_lines_elt_for_plan){
	foreach($prescription->_ref_lines_elt_for_plan as $elements_chap){
	  foreach($elements_chap as $name_cat => $elements_cat){
	    if(!isset($prescription->_nb_produit_by_cat[$name_cat])){
	      $prescription->_nb_produit_by_cat[$name_cat] = 0;
	    }
	    foreach($elements_cat as $_element){
	      foreach($_element as $element){
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
            (object_class = 'CPrescriptionLineMedicament')";

$where["sejour_id"] = " = '$sejour->_id'";
$transmissions_by_class = $transmission->loadList($where);

foreach($transmissions_by_class as $_transmission){
  $_transmission->loadRefsFwd();
	$prescription->_transmissions[$_transmission->object_class][$_transmission->object_id][$_transmission->_id] = $_transmission;
}

$signe_decalage = ($nb_decalage < 0) ? "-" : "+";

$real_date = mbDate();
$real_time = mbTime();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("heures", $heures);
$smarty->assign("signe_decalage"     , $signe_decalage);
$smarty->assign("nb_decalage"        , abs($nb_decalage));
$smarty->assign("hier"               , $hier);
$smarty->assign("demain"             , $demain);
$smarty->assign("poids"              , $poids);
$smarty->assign("patient"            , $patient);
$smarty->assign("prescription"       , $prescription);
$smarty->assign("tabHours"           , $tabHours);
$smarty->assign("sejour"             , $sejour);
$smarty->assign("prescription_id"    , $prescription_id);
$smarty->assign("date"               , $date);
$smarty->assign("now"                , $now);
$smarty->assign("categories"         , $categories);
$smarty->assign("real_date"          , $real_date);
$smarty->assign("real_time"          , $real_time);
$smarty->assign("categorie"          , new CCategoryPrescription());
$smarty->display("inc_vw_dossier_soins.tpl");

?>