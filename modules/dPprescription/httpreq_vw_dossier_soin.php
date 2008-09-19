<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
$date      = mbGetValueFromGetOrSession("date");
$nb_decalage = mbGetValueFromGetOrSession("nb_decalage",0);
$now       = mbDateTime();

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
  $prescription->_prises[$type] = array();
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
  $types = array("med", "elt");
 	foreach($types as $type){
 	  $prescription->_prises[$type] = array();
 	  $prescription->_lines[$type] = array();
 	  $prescription->_intitule_prise[$type] = array();
 	}

	// Chargement des lignes
	$prescription->loadRefsLinesMed("1","1");
	$prescription->loadRefsLinesElementByCat();
	$prescription->_ref_object->loadRefPrescriptionTraitement();
		 
	$traitement_personnel = $prescription->_ref_object->_ref_prescription_traitement;
	if($traitement_personnel->_id){
	  $traitement_personnel->loadRefsLinesMed("1","1");
	}
	  	  
	// Calcul du plan de soin pour la journée $date
  foreach($dates as $_date){
    foreach($types as $type){
  	  $prescription->_list_prises[$type][$_date] = array();
    }
    $prescription->calculPlanSoin($_date, 0, $heures);
  }
}	

// Calcul du rowspan pour les medicaments
$prescription->_nb_produit_by_cat["med"] = 0;
foreach($prescription->_lines["med"] as $_line){
  foreach($_line as $line_med){
    $prescription->_nb_produit_by_cat["med"]++;
  }
}

// Calcul du rowspan pour les elements
foreach($prescription->_lines["elt"] as $elements_chap){
  foreach($elements_chap as $name_cat => $elements_cat){
    if(!isset($this->_nb_produit_by_cat[$name_cat])){
      $prescription->_nb_produit_by_cat[$name_cat] = 0;
    }
    foreach($elements_cat as $_element){
      foreach($_element as $element){
        $prescription->_nb_produit_by_cat[$name_cat]++;
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