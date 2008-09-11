<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: $
* @author Alexis Granger
*/

$date = mbGetValueFromGetOrSession("date", mbDate());
$dateTime_min = mbGetValueFromGetOrSession("_dateTime_min", "$date 00:00:00");
$dateTime_max = mbGetValueFromGetOrSession("_dateTime_max", "$date 23:59:59");

$date_min = mbDate($dateTime_min);
$date_max = mbDate($dateTime_max);

// Filtres du sejour
$token_cat = mbGetValueFromGet("token_cat","");
$cats = explode("|",$token_cat);
$service_id = mbGetValueFromGetOrSession("service_id");

// Filtres sur l'heure des prises
$time_min = mbTime($dateTime_min, "00:00:00");
$time_max = mbTime($dateTime_max, "23:59:59");

// Stockage des jours concernés par le chargement
$dates = array();
$nb_days = mbDaysRelative($date_min, $date_max);
for($i=0; $i<=$nb_days; $i++){
  $dates[] = mbDate("+ $i DAYS", $date_min);
}

// Chargement de toutes les prescriptions
$where = array();
$ljoin = array();
$ljoin['sejour'] = 'prescription.object_id = sejour.sejour_id';
$ljoin['affectation'] = 'sejour.sejour_id = affectation.sejour_id';
$ljoin['lit'] = 'affectation.lit_id = lit.lit_id';
$ljoin['chambre'] = 'lit.chambre_id = chambre.chambre_id';
$ljoin['service'] = 'chambre.service_id = service.service_id';
$where['prescription.type'] = " = 'sejour'";
$where[] = "(sejour.entree_prevue BETWEEN '$date_min' AND '$date_max') OR 
            (sejour.sortie_prevue BETWEEN '$date_min' AND '$date_max') OR
            (sejour.entree_prevue <= '$date_min' AND sejour.sortie_prevue >= '$date_max')"; 
$where['service.service_id'] = " = '$service_id'";
$prescription = new CPrescription();
$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);
$lines = array();
$patients = array();

$lines_by_patient = array();

$list_heures = range(0,23);
foreach($list_heures as &$heure){
  $heure = str_pad($heure, 2, "0", STR_PAD_LEFT);
  $heures[$heure] = $heure;
}

$lines["med"] = array();
$lines["elt"] = array();

foreach($prescriptions as $_prescription){
  // Chargement des lignes
  $_prescription->loadRefsLinesMed("1");
  $_prescription->loadRefsLinesElementByCat();
  $_prescription->_ref_object->loadRefPrescriptionTraitement();	  
  $_prescription->_ref_object->_ref_prescription_traitement->loadRefsLinesMed("1");
  
  $sejour =& $_prescription->_ref_object;
  $sejour->loadRefPatient();
  $sejour->loadRefsOperations();
  $sejour->loadCurrentAffectation($date);
  $sejour->_ref_last_operation->loadRefPlageOp();

  $patient =& $sejour->_ref_patient;
  $patient->loadRefConstantesMedicales();
 
  // Stockage de la liste des patients
  $sejours[$sejour->_id] = $sejour;

  if(in_array("med", $cats)){
	foreach($_prescription->_ref_prescription_lines as $_line){
	  $lines["med"][$_line->_id] = $_line; 
	}
	foreach($_prescription->_ref_object->_ref_prescription_traitement->_ref_prescription_lines as $_line){
	  $lines["med"][$_line->_id] = $_line; 
	}
  }
  
  // Chargement des lignes d'elements
  $_prescription->loadRefsLinesElement();
  foreach($_prescription->_ref_prescription_lines_element as $_line_element){
    if(in_array($_line_element->_ref_element_prescription->category_prescription_id, $cats)){
	  $lines["elt"][$_line_element->_id] = $_line_element;
	}
  }  	

  foreach($dates as $_date){
    $_prescription->calculPlanSoin($_date, 1, $heures);
  }

  $patient_id = $sejour->_ref_patient->_id;
 if($_prescription->_list_prises){
 foreach($_prescription->_list_prises as $type => $prises){
  foreach($prises as $_date => $prises_by_date){
    foreach($prises_by_date as $line_id => $prises_by_unite){
      foreach($prises_by_unite as $unite_prise => $prises_by_hour)
        if($unite_prise != "total"){
          foreach($prises_by_hour as $_hour => $quantite){
            if(is_numeric($unite_prise)){
              $prise = new CPrisePosologie();
              $prise->load($unite_prise);
              $unite_prise = $prise->unite_prise;
            }
            $dateTimePrise = "$_date $_hour:00:00";
			if(array_key_exists($line_id, $lines[$type])){
	          if($dateTimePrise > $dateTime_min && $dateTimePrise < $dateTime_max) {
		        @$lines_by_patient[$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view][$sejour->_id][$_date][$_hour][$type][$line_id][$unite_prise] += $quantite;
	    	  } 
			}
          }  
        }
        // Tri par heures croissantes
        if(isset($lines_by_patient[$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view][$sejour->_id])){
          ksort($lines_by_patient[$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view][$sejour->_id][$_date]);
        }
      }
    }
  }
 }
}
  
ksort($lines_by_patient);

// Chargement de toutes les categories
$categories = CCategoryPrescription::loadCategoriesByChap();

// Initialisation des filtres
$prescription = new CPrescription();
$prescription->_dateTime_min = $dateTime_min;
$prescription->_dateTime_max = $dateTime_max;

// Reconstruction du tokenField
$token_cat = implode("|", $cats);

$cat_used = array();
foreach($cats as $_cat){
  if($_cat == "med"){
    $cat_used["med"] = "Médicament";
  } else {
    if(!array_key_exists($_cat, $cat_used)){
      $categorie = new CCategoryPrescription();
      $categorie->load($_cat);
      $cat_used[$categorie->_id] = $categorie->_view;
    } 
  }
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("cat_used", $cat_used);
$smarty->assign("token_cat", $token_cat);
$smarty->assign("cats", $cats);
$smarty->assign("dates", $dates);
$smarty->assign("categories", $categories);
$smarty->assign("prescription", $prescription);
$smarty->assign("sejours", $sejours);
$smarty->assign("lines_by_patient", $lines_by_patient);
$smarty->assign("lines", $lines);
$smarty->assign("dateTime_min", $dateTime_min);
$smarty->assign("dateTime_max", $dateTime_max);
$smarty->display('vw_bilan_service.tpl');

?>