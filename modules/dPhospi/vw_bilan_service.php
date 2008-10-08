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
$where[] = "(sejour.entree_prevue BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59') OR 
            (sejour.sortie_prevue BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59') OR
            (sejour.entree_prevue <= '$date_min 00:00:00' AND sejour.sortie_prevue >= '$date_max 23:59:59')";

$where['service.service_id'] = " = '$service_id'";
$prescription = new CPrescription();
$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);

$lines = array();
$patients = array();
$lines_by_patient = array();
$sejours = array();

$list_heures = range(0,23);
foreach($list_heures as &$heure){
  $heure = str_pad($heure, 2, "0", STR_PAD_LEFT);
  $heures[$heure] = $heure;
}

$lines["med"] = array();
$lines["elt"] = array();
$list_lines = array();
foreach($prescriptions as $_prescription){
  // Chargement des lignes
  $_prescription->loadRefsLinesMed("1","1","service");
  $_prescription->loadRefsLinesElementByCat("1","","service");
  $_prescription->_ref_object->loadRefPrescriptionTraitement();	  
  $_prescription->_ref_object->_ref_prescription_traitement->loadRefsLinesMed("1","1","service");
  
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
		foreach($_prescription->_ref_prescription_lines as $_line_med){
		  $lines["med"][$_line_med->_id] = $_line_med; 
		}
		foreach($_prescription->_ref_object->_ref_prescription_traitement->_ref_prescription_lines as $_line_trait){
		  $lines["med"][$_line_trait->_id] = $_line_trait; 
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

 $patient =& $sejour->_ref_patient;
 $patient_id = $patient->_id;
 if($_prescription->_list_prises){
 foreach($_prescription->_list_prises as $type => $prises){
  foreach($prises as $_date => $prises_by_date){
    foreach($prises_by_date as $line_id => $prises_by_unite){
      $line_class = ($type == "med") ? "CPrescriptionLineMedicament" : "CPrescriptionLineElement";
      if(isset($list_lines[$type][$line_id])){
        $line = $list_lines[$type][$line_id];
      } else {
        $line = new $line_class;
        $line->load($line_id);
      }
      $line->calculAdministrations($_date);
      $list_lines[$type][$line->_id] = $line;
      
      foreach($prises_by_unite as $unite_prise => $prises_by_hour)
        if($unite_prise != "total"){
          foreach($prises_by_hour as $_hour => $quantite){
            if(is_numeric($unite_prise)){
              $prise = new CPrisePosologie();
              $prise->load($unite_prise);
              $unite_prise = $prise->unite_prise;
            }
            // On supprime le kg de l'unite de prise si le poids du patient est indiqué (quantite calculée dans calculPrises())
            if($patient->_ref_constantes_medicales->poids){
              $unite_prise = str_replace('/kg', '', $unite_prise);
            }
           
            if($unite_prise)
            $dateTimePrise = "$_date $_hour:00:00";
			      if(array_key_exists($line_id, $lines[$type])){
	            if($dateTimePrise > $dateTime_min && $dateTimePrise < $dateTime_max) {
	              @$lines_by_patient[$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view][$sejour->_id][$_date][$_hour][$type][$line_id][$unite_prise]["prevu"] += $quantite;
	            }
			      }
          }
        }
        // Tri par heures croissantes
        if(isset($lines_by_patient[$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view][$sejour->_id][$_date])){
          ksort($lines_by_patient[$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view][$sejour->_id][$_date]);
        }
      }
    }
  }
 }
}

// Reorganisation des administrations
foreach($list_lines as $_lines_by_type){
  foreach($_lines_by_type as $curr_line){
    $curr_line->loadRefPrescription();
    $curr_line->_ref_prescription->loadRefObject();
    $sejour =& $curr_line->_ref_prescription->_ref_object;
    $sejour->loadCurrentAffectation($date);
    $sejour->loadRefPatient();
    $patient =& $sejour->_ref_patient;
    $patient->loadRefConstantesMedicales();
    $type = ($curr_line->_class_name == "CPrescriptionLineMedicament") ? "med" : "elt";
    if($curr_line->_administrations){
	    foreach($curr_line->_administrations as $unite_prise => $lines){
	      if(is_numeric($unite_prise)){
          $prise = new CPrisePosologie();
          $prise->load($unite_prise);
          $unite_prise = $prise->unite_prise;
			  }        
        // On supprime le kg de l'unite de prise si le poids du patient est indiqué (quantite calculée dans calculPrises())
        if($patient->_ref_constantes_medicales->poids){
          $unite_prise = str_replace('/kg', '', $unite_prise);
        }
            
	      foreach($lines as $_date => $lines_by_date){
	        foreach($lines_by_date as $hour => $_line){
	          // Quantite administre => $_line["quantite"];
	          $administrations[$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view][$sejour->_id][$_date][$hour][$type][$curr_line->_id][$unite_prise]["administre"] = $_line["quantite"];
	        }
	      }
	    }
    }
  }
}

// Fusion des deux tableaux
foreach($lines_by_patient as $chambre_view => &$lines_by_sejour){
  foreach($lines_by_sejour as $sejour_id => &$lines_by_date){
    foreach($lines_by_date as $_date => &$lines_by_hours){
      if(isset($administrations[$chambre_view][$sejour_id][$_date])){
	      foreach($administrations[$chambre_view][$sejour_id][$_date] as $hour_adm => $adm){
	        if(!array_key_exists($hour_adm, $lines_by_hours)){
	          $lines_by_date[$_date][$hour_adm] = $administrations[$chambre_view][$sejour_id][$_date][$hour_adm];
	        }
	      }
      }
      foreach($lines_by_hours as $_hour => &$lines_by_type){
        foreach($lines_by_type as $type => &$lines_by_unite){
          foreach($lines_by_unite as $line_id => &$lines_by_type_prise){
            foreach($lines_by_type_prise as $unite_prise => &$_line){  
              @$_line["administre"] = $administrations[$chambre_view][$sejour_id][$_date][$_hour][$type][$line_id][$unite_prise]["administre"];
            }
          }
        }
      }
     ksort($lines_by_hours);
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
$smarty->assign("list_lines", $list_lines);
$smarty->assign("dateTime_min", $dateTime_min);
$smarty->assign("dateTime_max", $dateTime_max);
$smarty->display('vw_bilan_service.tpl');

?>