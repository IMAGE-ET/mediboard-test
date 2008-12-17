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

$lines_by_patient = array();
$sejours = array();
$list_lines = array();
$chambres = array();

if (mbGetValueFromGet("do")) {
	// Chargement de toutes les prescriptions
	$where = array();
	$ljoin = array();
	$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id";
	$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
	$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
	$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
	$ljoin["service"] = "chambre.service_id = service.service_id";
	$where["prescription.type"] = " = 'sejour'";
	$where[] = "(sejour.entree_prevue BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59') OR 
	            (sejour.sortie_prevue BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59') OR
	            (sejour.entree_prevue <= '$date_min 00:00:00' AND sejour.sortie_prevue >= '$date_max 23:59:59')";
	
	$where["service.service_id"] = " = '$service_id'";
	$prescription = new CPrescription();
	$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);
	
	$lines = array();
	$patients = array();
	
	$lines["med"] = array();
	$lines["elt"] = array();
	foreach($prescriptions as $_prescription){
	  // Chargement des lignes
	  $_prescription->loadRefsLinesMed("1","1","service");
	  $_prescription->loadRefsLinesElementByCat("1","","service");
	  $_prescription->_ref_object->loadRefPrescriptionTraitement();	  
	  $_prescription->_ref_object->_ref_prescription_traitement->loadRefsLinesMed("1","1","service");
	
	  // Chargement des perfusions 
	  $_prescription->loadRefsPerfusions();
	  
	  foreach($dates as $_date){
	    $_prescription->calculPlanSoin($_date, 0);
	  }
	  
	  $sejour =& $_prescription->_ref_object;
	  $sejour->loadRefsOperations();
	  $sejour->_ref_last_operation->loadRefPlageOp();
	  // Stockage de la liste des patients
	  $sejours[$sejour->_id] = $sejour;
	  
	  
	  $sejour->loadRefPatient();
	  $patient =& $sejour->_ref_patient;
	  $patient->loadRefConstantesMedicales();
	  
	  
	  if(in_array("med", $cats)){
	    // Parcours et stockage des perfusions
	    if($_prescription->_ref_perfusions){
	      foreach($_prescription->_ref_perfusions as $_perfusion){
	        $_perfusion->loadRefsLines();
	        $affectation = $sejour->getCurrAffectation($_perfusion->_debut);
	        $affectation->loadRefLit();
	        $affectation->_ref_lit->loadCompleteView();    
	        $chambre = $affectation->_ref_lit->_ref_chambre;
	        if(!$chambre){
	          continue;
	        }
	        $chambres[$chambre->_id] = $chambre;
	        if(in_array($_perfusion->date_debut, $dates)){
	          $lines_by_patient[$chambre->_id][$sejour->_id][$_perfusion->date_debut]["perf"] = $_perfusion;
	        }
	      }
	    }
	  
	    // Parcours des medicament du plan de soin  
	    if($_prescription->_ref_lines_med_for_plan){
				foreach($_prescription->_ref_lines_med_for_plan as $_code_ATC => &$_cat_ATC){
				  foreach($_cat_ATC as &$_lines_by_unite) {
				    foreach($_lines_by_unite as &$_line_med){
				      $list_lines[$_line_med->_class_name][$_line_med->_id] = $_line_med;
				      // Prises prevues
			        if(is_array($_line_med->_quantity_by_date)){
					      foreach($_line_med->_quantity_by_date as $unite_prise => &$prises_prevues_by_unite){
					        foreach($prises_prevues_by_unite as $_date => &$prises_prevues_by_date){
					          if(is_array($prises_prevues_by_date['quantites'])){
						          foreach($prises_prevues_by_date['quantites'] as $_hour => &$prise_prevue){
						            if(!isset($affectations[$sejour->_id]["$_date $_hour:00:00"])){
							            $sejour->loadRefCurrAffectation("$_date $_hour:00:00");
							            $chambre =& $sejour->_ref_curr_affectation->_ref_lit->_ref_chambre;
							            if(!$chambre){
							              continue;
							            }
		                      $chambres[$chambre->_id] = $chambre;
	                        $affectations[$sejour->_id]["$_date $_hour:00:00"] = $sejour->_ref_curr_affectation;
						            } else {
						              $affectation = $affectations[$sejour->_id]["$_date $_hour:00:00"];
						              $chambre = $affectation->_ref_lit->_ref_chambre;
						            }
						            if($prise_prevue["total"]){
						              @$lines_by_patient[$chambre->_id][$sejour->_id][$_date][$_hour][$_line_med->_class_name][$_line_med->_id]["prevu"] += $prise_prevue["total"];
						              $prise_prevue["total"] = 0;
						            }			            
						          }
					          }
					        }
					      }
			        }
				      // Administration effectuees
				      if(is_array($_line_med->_administrations)){
					      foreach($_line_med->_administrations as $unite_prise => &$administrations_by_unite){
					        foreach($administrations_by_unite as $_date => &$administrations_by_date){
					          foreach($administrations_by_date as $_hour => &$administrations_by_hour){
					            if(is_numeric($_hour)){
					          		if(!isset($affectations[$sejour->_id]["$_date $_hour:00:00"])){
							            $sejour->loadRefCurrAffectation("$_date $_hour:00:00");
							            $chambre =& $sejour->_ref_curr_affectation->_ref_lit->_ref_chambre;
					          			if(!$chambre){
								            continue;
								          }
		                      $chambres[$chambre->_id] = $chambre;
		                       $affectations[$sejour->_id]["$_date $_hour:00:00"] = $sejour->_ref_curr_affectation;
						            } else {
						              $affectation = $affectations[$sejour->_id]["$_date $_hour:00:00"];
						              $chambre = $affectation->_ref_lit->_ref_chambre;
						            }
		                     
						            $quantite = @$administrations_by_hour["quantite"];
						            if($quantite){
						              @$lines_by_patient[$chambre->_id][$sejour->_id][$_date][$_hour][$_line_med->_class_name][$_line_med->_id]["administre"] += $quantite;
						              $administrations_by_hour["quantite"] = 0;
						            }
						            $quantite_planifiee = @$administrations_by_hour["quantite_planifiee"];
						            if($quantite_planifiee){
						              @$lines_by_patient[$chambre->_id][$sejour->_id][$_date][$_hour][$_line_med->_class_name][$_line_med->_id]["prevu"] += $quantite_planifiee;
						              $administrations_by_hour["quantite_planifiee"] = 0;
						            }
					            }
					          }
					        }
					      }
				      }
				    }
				  }
				} 
		  }
	  }
		// Parcours des elements du plan de soin
	  if($_prescription->_ref_lines_elt_for_plan){
		  foreach($_prescription->_ref_lines_elt_for_plan as &$elements_chap){
			  foreach($elements_chap as $name_cat => &$elements_cat){
			    if(!in_array($name_cat, $cats)){
			      continue;
			    }
			    foreach($elements_cat as &$_element){
			      foreach($_element as &$_line_elt){
				      $list_lines[$_line_elt->_class_name][$_line_elt->_id] = $_line_elt;
				      // Prises prevues
			        if(is_array($_line_elt->_quantity_by_date)){
					      foreach($_line_elt->_quantity_by_date as $unite_prise => &$prises_prevues_by_unite){
					        foreach($prises_prevues_by_unite as $_date => &$prises_prevues_by_date){
					          if(is_array($prises_prevues_by_date['quantites'])){
						          foreach($prises_prevues_by_date['quantites'] as $_hour => &$prise_prevue){
	                      if(!isset($affectations[$sejour->_id]["$_date $_hour:00:00"])){
							            $sejour->loadRefCurrAffectation("$_date $_hour:00:00");
							            $chambre =& $sejour->_ref_curr_affectation->_ref_lit->_ref_chambre;
	                      	if(!$chambre){
							              continue;
							            }
		                      $chambres[$chambre->_id] = $chambre;
		                       $affectations[$sejour->_id]["$_date $_hour:00:00"] = $sejour->_ref_curr_affectation;
						            } else {
						              $affectation = $affectations[$sejour->_id]["$_date $_hour:00:00"];
						              $chambre = $affectation->_ref_lit->_ref_chambre;
						            }
	
						            if($prise_prevue["total"]){
						              @$lines_by_patient[$chambre->_id][$sejour->_id][$_date][$_hour][$_line_elt->_class_name][$_line_elt->_id]["prevu"] += $prise_prevue["total"];
						              $prise_prevue = 0;
						            }
						          }
					          }
					        }
					      }
			        }
				      // Administration effectuees
				      if(is_array($_line_elt->_administrations)){
					      foreach($_line_elt->_administrations as $unite_prise => &$administrations_by_unite){
					        foreach($administrations_by_unite as $_date => &$administrations_by_date){
					          foreach($administrations_by_date as $_hour => &$administrations_by_hour){
	                    if(is_numeric($hour)){
						            if(!isset($affectations[$sejour->_id]["$_date $_hour:00:00"])){
							            $sejour->loadRefCurrAffectation("$_date $_hour:00:00");
							            $chambre =& $sejour->_ref_curr_affectation->_ref_lit->_ref_chambre;
		                    	if(!$chambre){
								            continue;
								          }
		                      $chambres[$chambre->_id] = $chambre;
		                      $affectations[$sejour->_id]["$_date $_hour:00:00"] = $sejour->_ref_curr_affectation;
						            } else {
						              $affectation = $affectations[$sejour->_id]["$_date $_hour:00:00"];
						              $chambre = $affectation->_ref_lit->_ref_chambre;
						            }
		
						            $quantite = $administrations_by_hour["quantite"];
						            if($quantite){
						              @$lines_by_patient[$chambre->_id][$sejour->_id][$_date][$_hour][$_line_elt->_class_name][$_line_elt->_id]["administre"] += $quantite;
						              $administrations_by_hour["quantite"] = 0;
						            }
						          	$quantite_planifiee = @$administrations_by_hour["quantite_planifiee"];
							          if($quantite_planifiee){
							            @$lines_by_patient[$chambre->_id][$sejour->_id][$_date][$_hour][$_line_elt->_class_name][$_line_elt->_id]["prevu"] += $quantite_planifiee;
							            $administrations_by_hour["quantite_planifiee"] = 0;
							          }
	                    }
					          }
					        }
					      }
				      }
				    }
				  }
				}
		  }
	  }
	}
}

// Tri des lignes
foreach($lines_by_patient as $chambre_view => &$lines_by_sejour){
  foreach($lines_by_sejour as $sejour_id => &$lines_by_date){
    ksort($lines_by_date);
    foreach($lines_by_date as $date => &$lines_by_hours){
      ksort($lines_by_hours);
    }
  }
}



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
$smarty->assign("chambres", $chambres);
$smarty->assign("dateTime_min", $dateTime_min);
$smarty->assign("dateTime_max", $dateTime_max);
$smarty->display('vw_bilan_service.tpl');

?>