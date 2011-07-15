<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function getCurrentLit($sejour, $_date, $_hour, &$lits, &$affectations){
  if(!isset($affectations[$sejour->_id]["$_date $_hour:00:00"])){
    $sejour->loadRefCurrAffectation("$_date $_hour:00:00");
    $lit =& $sejour->_ref_curr_affectation->_ref_lit;
		if($lit){
      $lits[$lit->_ref_chambre->nom] = $lit;
      $affectations[$sejour->_id]["$_date $_hour:00:00"] = $sejour->_ref_curr_affectation;
    }
  } else {
    $affectation = $affectations[$sejour->_id]["$_date $_hour:00:00"];
    $lit = $affectation->_ref_lit;
  }
  if ($lit) {
    $lit->loadCompleteView();
  }
  return $lit;
}			            

$periode       = CValue::get("periode");
$service_id    = CValue::getOrSession("service_id");
$by_patient    = CValue::get("by_patient", false);
$show_inactive = CValue::get("show_inactive", "0");
$_present_only = CValue::get("_present_only", 1);
$offline       = CValue::get("offline", 0);
$date          = CValue::getOrSession("date", mbDate());
$do            = CValue::get("do");

if($offline){
	$by_patient = true;
	$do = 1;
  $dateTime_min = mbDateTime(" - 12 HOURS");
	$dateTime_max = mbDateTime(" + 24 HOURS");
} else {
	$dateTime_min = CValue::getOrSession("_dateTime_min", "$date 00:00:00");
  $dateTime_max = CValue::getOrSession("_dateTime_max", "$date 23:59:59");
}

$date_min = mbDate($dateTime_min);
$date_max = mbDate($dateTime_max);

// Filtres du sejour
$token_cat = CValue::get("token_cat","");

if ($token_cat == "all") {
  $token_cat = "trans|med|inj|perf|aerosol|stup";
  $categories = CCategoryPrescription::loadCategoriesByChap(null, "current");
  
  foreach ($categories as $categories_by_chap) {
    foreach($categories_by_chap as $category_id => $_categorie) {
      $token_cat .= "|$category_id";
    }
  }
}

$elts = $cats = explode("|",$token_cat);

CMbArray::removeValue("med", $elts);
CMbArray::removeValue("perf", $elts);
CMbArray::removeValue("inj", $elts);
CMbArray::removeValue("trans", $elts);

$do_elements    = (count($elts) > 0);
$do_medicaments = (in_array("med"    , $cats));
$do_injections  = (in_array("inj"    , $cats));
$do_perfusions  = (in_array("perf"   , $cats));
$do_aerosols    = (in_array("aerosol", $cats));
$do_stupefiants = (in_array("stup"   , $cats));
$do_trans       = (in_array("trans", $cats));

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

$lits = array();
$affectations = array();

$_transmissions = array();
$_observations = array();
$_constantes = array();

$transmissions = array();
$observations = array();
$trans_obs = array();

if($do_trans){
  $where = array();
  $ljoin = array();

  $ljoin["sejour"] = "transmission_medicale.sejour_id = sejour.sejour_id";
	$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $ljoin["lit"] = "affectation.lit_id = lit.lit_id";
  $ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
  $ljoin["service"] = "chambre.service_id = service.service_id";
  
  $where[] = "(degre = 'high') OR (date >= '$dateTime_min' AND date <= '$dateTime_max')";
  $where[] = "affectation.entree >= (
    SELECT max(a2.entree) FROM affectation a2
    WHERE a2.sejour_id = affectation.sejour_id)";
  $where["service.service_id"] = " = '$service_id'";
  
  $where[] = "(sejour.entree BETWEEN '$dateTime_min' AND '$dateTime_max') OR 
            (sejour.sortie BETWEEN '$dateTime_min' AND '$dateTime_max') OR
            (sejour.entree <= '$dateTime_min' AND sejour.sortie >= '$dateTime_max')";
  
  if ($_present_only) {
    $where["sejour.sortie_reelle"] = 'IS NULL';
  }
  
  $order_by = "chambre.nom, date DESC";

  $transmission = new CTransmissionMedicale();
  $_transmissions = $transmission->loadList($where, $order_by, null, null, $ljoin);
	
	$ljoin["sejour"] = "observation_medicale.sejour_id = sejour.sejour_id";
  $observation = new CObservationMedicale();
  $_observations = $observation->loadList($where, $order_by, null, null, $ljoin);
  
  // On retire le degré dans le where
  unset($where[0]);
  
  $where[] = "(datetime >= '$dateTime_min ' AND datetime <= '$dateTime_max')";
  $ljoin["sejour"] = "constantes_medicales.context_id = sejour.sejour_id";
  $where["context_class"] = " = 'CSejour'";
  $order_by = "chambre.nom, datetime DESC";
  $constante = new CConstantesMedicales();
  $_constantes = $constante->loadlist($where, $order_by, null, null, $ljoin);
}

$patients = array();
$trans_and_obs = array();

foreach($_transmissions as $_trans){
	$_trans->loadRefSejour();
	$sejour =& $_trans->_ref_sejour;
	$sejour->loadRefsOperations();
  $sejour->_ref_last_operation->loadRefPlageOp();
  $sejour->_ref_last_operation->loadRefChir();
  $sejour->loadRefsAffectations();
  
  foreach($sejour->_ref_affectations as $_affectation) {
    $_affectation->loadView();
  }
  
  $sejour->_ref_last_operation->loadExtCodesCCAM();
		
	$patient_id = $sejour->patient_id;

	if(!array_key_exists($patient_id, $patients)){
    $sejour->loadRefPatient();
    $sejour->_ref_patient->loadRefConstantesMedicales();
		$patients[$patient_id] = $sejour->_ref_patient;
	}
	$_trans->loadRefsFwd();
	if($_trans->object_id){
	  $_trans->_ref_object->loadRefsFwd();
	}
	
	$trans_and_obs[$patient_id][$_trans->date][] = $_trans;
}

foreach($_observations as $_obs){
  $_obs->loadRefSejour();
	$sejour =& $_obs->_ref_sejour;
  $sejour->loadRefsOperations();

  $sejour->_ref_last_operation->loadRefPlageOp();
  $sejour->_ref_last_operation->loadRefChir();
  $sejour->loadRefsAffectations();
  
  foreach($sejour->_ref_affectations as $_affectation) {
    $_affectation->loadView();
  }
  
  $sejour->_ref_last_operation->loadExtCodesCCAM();
  
  $patient_id = $sejour->patient_id;
  
  if(!array_key_exists($patient_id, $patients)){
    $sejour->loadRefPatient();
    $sejour->_ref_patient->loadRefConstantesMedicales();
    $patients[$patient_id] = $sejour->_ref_patient;
  }

  $_obs->loadRefsFwd();

	$trans_and_obs[$patient_id][$_obs->date][] = $_obs;
}

foreach($_constantes as $_constante) {
  $_constante->loadRefsFwd();
  $_constante->loadRefUser();
  $sejour =& $_constante->_ref_context;
  $sejour->loadRefsOperations();
  $sejour->_ref_last_operation->loadRefPlageOp();
  $sejour->_ref_last_operation->loadRefChir();
  $sejour->loadRefsAffectations();
  
  foreach($sejour->_ref_affectations as $_affectation) {
    $_affectation->loadView();
  }
  
  $sejour->_ref_last_operation->loadExtCodesCCAM();

  $patient_id = $sejour->patient_id;
  if(!array_key_exists($patient_id, $patients)){
    $sejour->loadRefPatient();
    $sejour->_ref_patient->loadRefConstantesMedicales();
    $patients[$patient_id] = $sejour->_ref_patient;
  }
  $trans_and_obs[$patient_id][$_constante->datetime][] = $_constante;
}

// Tri des transmission, observations et constantes par date décroissante
foreach($trans_and_obs as &$_trans) {
  krsort($_trans, SORT_STRING);
}

if ($do && ($do_medicaments || $do_injections || $do_perfusions || $do_aerosols || $do_elements || $do_stupefiants)) {
	// Chargement de toutes les prescriptions
	$where = array();
	$ljoin = array();
	$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id";
	$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
	$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
	$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
	$ljoin["service"] = "chambre.service_id = service.service_id";
	$where["prescription.type"] = " = 'sejour'";
  $where["sejour.entree"]      = " <= '$date_max 23:59:59'";
  $where["sejour.sortie"]      = " >= '$date_min 00:00:00'"; 

  if ($_present_only == "true") {
    $where["sejour.sortie_reelle"] = 'IS NULL';
  }
	
	$where["service.service_id"] = " = '$service_id'";
	$orderby = "chambre.nom";
	$prescription = new CPrescription();
	$prescriptions = $prescription->loadList($where, $orderby, null, null, $ljoin);
	
	$lines = array();
	$lines["med"] = array();
	$lines["elt"] = array();
	foreach($prescriptions as $_prescription){
	  // Chargement des lignes
	  $_prescription->loadRefsLinesMed("1","1","service");
		if ($do_elements) {
	    $_prescription->loadRefsLinesElementByCat("1","","service");
	  }
	  if($do_perfusions || $do_aerosols || $do_stupefiants){
	    $_prescription->loadRefsPrescriptionLineMixes();
	  }
		// Calcul du plan de soin
	  $_prescription->calculPlanSoin($dates);
	  
	  // Chargement du sejour et du patient
	  $sejour =& $_prescription->_ref_object;
	  
	  // Si les transmissions ont été requêtées,
	  // alors le loadRefsOperations peut avoir déjà été fait
	  if (!isset($sejour->_ref_operations)) {
	    $sejour->loadRefsOperations();
	    $sejour->_ref_last_operation->loadRefPlageOp();
	    $sejour->_ref_last_operation->loadExtCodesCCAM();
	  }

	  // Stockage de la liste des patients
	  $sejours[$sejour->_id] = $sejour;
	  $sejour->loadRefPatient();
	  $patient =& $sejour->_ref_patient;
	  $patient->loadRefConstantesMedicales();
	  
	  if($do_medicaments || $do_injections || $do_perfusions || $do_aerosols || $do_stupefiants){
	    if($do_perfusions || $do_aerosols || $do_stupefiants){
				// Parcours et stockage des prescription_line_mixes
		    if($_prescription->_ref_prescription_line_mixes_for_plan){
		      foreach($_prescription->_ref_prescription_line_mixes_for_plan as $_prescription_line_mix){
		      	if($_prescription_line_mix->type_line == "aerosol" && !$do_aerosols && !$do_stupefiants){
		      		continue;
		      	}
						if($_prescription_line_mix->type_line == "perfusion" && !$do_perfusions && !$do_stupefiants){
              continue;
            }
						if($_prescription_line_mix->type_line == "oxygene"){
							continue;
						}
            
            $list_lines[$_prescription_line_mix->_class_name][$_prescription_line_mix->_id] = $_prescription_line_mix;
		        // Prises prevues
		        if(is_array($_prescription_line_mix->_prises_prevues)){
			        foreach($_prescription_line_mix->_prises_prevues as $_date => $_prises_prevues_by_hour){
			          foreach($_prises_prevues_by_hour as $_hour => $_prise_prevue){
                  $dateTimePrise = "$_date $_hour:00:00";
                  if($dateTimePrise < $dateTime_min || $dateTimePrise > $dateTime_max){
                    continue;
                  }
			            $lit = getCurrentLit($sejour, $_date, $_hour, $lits, $affectations);
			            if(!$lit) continue;
									
									foreach($_prescription_line_mix->_ref_lines as $_perf_line){
    							  if (!$_perf_line->stupefiant && $do_stupefiants) {
                      continue;
                    }
										$key1 = $by_patient ? $lit->_ref_chambre->nom : "med";
										$key2 = $by_patient ? "med" : $lit->_ref_chambre->nom;
                    
			              $list_lines[$_perf_line->_class_name][$_perf_line->_id] = $_perf_line;
										
										// Plusieurs prises pdt la meme heure
										if(array_key_exists("real_hour", $_prescription_line_mix->_prises_prevues[$_date][$_hour])){
										  $count_prises_by_hour = count($_prescription_line_mix->_prises_prevues[$_date][$_hour]["real_hour"]);
										  $lines_by_patient[$key1][$key2][$sejour->_id][$_date][$_hour]['CPrescriptionLineMix'][$_prescription_line_mix->_id][$_perf_line->_id]["prevu"] = $_perf_line->_quantite_administration * $count_prises_by_hour;
										}
									
								    if(array_key_exists("manual", $_prescription_line_mix->_prises_prevues[$_date][$_hour])){
											$lines_by_patient[$key1][$key2][$sejour->_id][$_date][$_hour]['CPrescriptionLineMix'][$_prescription_line_mix->_id][$_perf_line->_id]["prevu"] = $_prescription_line_mix->_prises_prevues[$_date][$_hour]["manual"][$_perf_line->_id];
                    }
									}
			          }
			        }
		        }
		        // Administrations effectuees
		        foreach($_prescription_line_mix->_ref_lines as $_perf_line){
		        	$_perf_line->loadRefProduitPrescription();
		          $list_lines[$_perf_line->_class_name][$_perf_line->_id] = $_perf_line;
		          if(is_array($_perf_line->_administrations)){
			          foreach($_perf_line->_administrations as $_date => $_adm_by_hour){
				          foreach($_adm_by_hour as $_hour => $_adm){
                    $dateTimePrise = "$_date $_hour:00:00";
                    if($dateTimePrise < $dateTime_min || $dateTimePrise > $dateTime_max){
                      continue;
                    }
									  $lit = getCurrentLit($sejour, $_date, $_hour, $lits, $affectations);
				            if(!$lit) continue;			              
				            
                    $key1 = $by_patient ? $lit->_ref_chambre->nom : "med";
                    $key2 = $by_patient ? "med" : $lit->_ref_chambre->nom;
                    
									  $lines_by_patient[$key1][$key2][$sejour->_id][$_date][$_hour]['CPrescriptionLineMix'][$_prescription_line_mix->_id][$_perf_line->_id]["administre"] = $_adm;
				          }
				        }
		          }
		        }
		      }
		    }
	    }
	    
	    // Parcours des medicament du plan de soin  
      $medicaments = array();
      if($do_medicaments || $do_stupefiants){
        $medicaments["med"] = $_prescription->_ref_lines_med_for_plan;
      }
      if($do_injections || $do_stupefiants){
        $medicaments["inj"] = $_prescription->_ref_injections_for_plan;
      }

      if($do_medicaments || $do_injections || $do_stupefiants){
		    foreach($medicaments as $type_med => $_medicaments){
			    if($_medicaments){
						foreach($_medicaments as $_code_ATC => &$_cat_ATC){
						  foreach($_cat_ATC as &$_lines_by_unite) {
						    foreach($_lines_by_unite as &$_line_med){
						      if (!$_line_med->stupefiant && $do_stupefiants) {
						        continue;
						      }
						    	$_line_med->loadRefProduitPrescription();
						      $list_lines[$_line_med->_class_name][$_line_med->_id] = $_line_med;
						      // Prises prevues
					        if(is_array($_line_med->_quantity_by_date)){
							      foreach($_line_med->_quantity_by_date as $unite_prise => &$prises_prevues_by_unite){
							        foreach($prises_prevues_by_unite as $_date => &$prises_prevues_by_date){
							          if(@is_array($prises_prevues_by_date['quantites'])){
								          foreach($prises_prevues_by_date['quantites'] as $_hour => &$prise_prevue){
								          	$dateTimePrise = "$_date $_hour:00:00";
														 if($dateTimePrise < $dateTime_min || $dateTimePrise > $dateTime_max){
	                             continue;
	                           }
								            $lit = getCurrentLit($sejour, $_date, $_hour, $lits, $affectations);
			                      if(!$lit) continue;
								            if($prise_prevue["total"]){
								            	
                              $key1 = $by_patient ? $lit->_ref_chambre->nom : "med";
                              $key2 = $by_patient ? "med" : $lit->_ref_chambre->nom;
    
								              @$lines_by_patient[$key1][$key2][$sejour->_id][$_date][$_hour][$_line_med->_class_name][$_line_med->_id]["prevu"] += $prise_prevue["total"];
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
						                $dateTimePrise = "$_date $_hour:00:00";
                            if($dateTimePrise < $dateTime_min || $dateTimePrise > $dateTime_max){
                               continue;
                            }
								            $lit = getCurrentLit($sejour, $_date, $_hour, $lits, $affectations);
								            if(!$lit) continue;			  
								            $quantite = @$administrations_by_hour["quantite"];
								            $key1 = $by_patient ? $lit->_ref_chambre->nom : "med";
                            $key2 = $by_patient ? "med" : $lit->_ref_chambre->nom;
								            if($quantite){
								              @$lines_by_patient[$key1][$key2][$sejour->_id][$_date][$_hour][$_line_med->_class_name][$_line_med->_id]["administre"] += $quantite;
								              $administrations_by_hour["quantite"] = 0;
								            }
								            $quantite_planifiee = @$administrations_by_hour["quantite_planifiee"];
								            if($quantite_planifiee){
								              @$lines_by_patient[$key1][$key2][$sejour->_id][$_date][$_hour][$_line_med->_class_name][$_line_med->_id]["prevu"] += $quantite_planifiee;
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
		// Parcours des elements du plan de soin
	  if($_prescription->_ref_lines_elt_for_plan){
		  foreach($_prescription->_ref_lines_elt_for_plan as $name_chap => &$elements_chap){
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
					          if(@is_array($prises_prevues_by_date['quantites'])){
						          foreach($prises_prevues_by_date['quantites'] as $_hour => &$prise_prevue){
			          	     $dateTimePrise = "$_date $_hour:00:00";
                       if($dateTimePrise < $dateTime_min || $dateTimePrise > $dateTime_max){
                         continue;
                       }
						            $lit = getCurrentLit($sejour, $_date, $_hour, $lits, $affectations);
						            if(!$lit) continue;			  
						            if($prise_prevue["total"]){

													$key1 = $by_patient ? $lit->_ref_chambre->nom : $name_chap;
													$key2 = $by_patient ? $name_chap : $lit->_ref_chambre->nom;
                    													
						              @$lines_by_patient[$key1][$key2][$sejour->_id][$_date][$_hour][$_line_elt->_class_name][$_line_elt->_id]["prevu"] += $prise_prevue["total"];
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
	                    if(is_numeric($_hour)){
                  	     $dateTimePrise = "$_date $_hour:00:00";
                         if($dateTimePrise < $dateTime_min || $dateTimePrise > $dateTime_max){
                           continue;
                         }
														 
						            $lit = getCurrentLit($sejour, $_date, $_hour, $lits, $affectations);
						            if(!$lit) continue;			  
						            $quantite = @$administrations_by_hour["quantite"];
						            if($quantite){
						            	
                          $key1 = $by_patient ? $lit->_ref_chambre->nom : $name_chap;
                          $key2 = $by_patient ? $name_chap : $lit->_ref_chambre->nom;
													
						              @$lines_by_patient[$key1][$key2][$sejour->_id][$_date][$_hour][$_line_elt->_class_name][$_line_elt->_id]["administre"] += $quantite;
						              $administrations_by_hour["quantite"] = 0;
						            }
						          	$quantite_planifiee = @$administrations_by_hour["quantite_planifiee"];
							          if($quantite_planifiee){
							          	
                          $key1 = $by_patient ? $lit->_ref_chambre->nom : $name_chap;
                          $key2 = $by_patient ? $name_chap : $lit->_ref_chambre->nom;
													
							            @$lines_by_patient[$key1][$key2][$sejour->_id][$_date][$_hour][$_line_elt->_class_name][$_line_elt->_id]["prevu"] += $quantite_planifiee;
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
ksort($lines_by_patient, SORT_STRING);
foreach($lines_by_patient as $name_chap => &$lines_by_chap){
	ksort($lines_by_chap, SORT_STRING);
	foreach($lines_by_chap as $lit_view => &$lines_by_sejour){
	  foreach($lines_by_sejour as $sejour_id => &$lines_by_date){
	    ksort($lines_by_date);
	    foreach($lines_by_date as $date => &$lines_by_hours){
	      ksort($lines_by_hours);
	    }
	  }
	}
}

// Chargement de toutes les categories
$categories = CCategoryPrescription::loadCategoriesByChap(null, "current");

// Initialisation des filtres
$prescription = new CPrescription();
$prescription->_dateTime_min = $dateTime_min;
$prescription->_dateTime_max = $dateTime_max;

// Reconstruction du tokenField
$token_cat = implode("|", $cats);

$cat_used = array();
foreach($cats as $_cat){
  if($_cat === "med" || $_cat === "inj" || $_cat === "perf" || $_cat == "aerosol"){
    $cat_used["med"][$_cat] = CAppUI::tr("CPrescription._chapitres.".$_cat);
  } else {
    if(!array_key_exists($_cat, $cat_used)){
      $categorie = new CCategoryPrescription();
      $categorie->load($_cat);
      $cat_used[$categorie->chapitre][$categorie->_id] = $categorie->_view;
    } 
  }
}

// Chargement de tous les groupes de categories de prescription de l'etablissement courant
$all_groups = array();
$cat_group = new CPrescriptionCategoryGroup();
$group_id = CGroups::loadCurrent()->_id;
$where = array();
$where["group_id"] = " = '$group_id'";
$cat_groups = $cat_group->loadList($where, "libelle");

foreach($cat_groups as $_cat_group){
	$_cat_group->loadRefsCategoryGroupItems();
  foreach($_cat_group->_ref_category_group_items as $_item){
  	$all_groups[$_cat_group->_id][] = $_item->category_prescription_id ? $_item->category_prescription_id : $_item->type_produit;
  }
}
// Chargement du service
$service = new CService();
$service->load($service_id);


$smarty = new CSmartyDP();
$smarty->assign("trans_and_obs"   , $trans_and_obs);
$smarty->assign("service"         , $service);
$smarty->assign("periode"         , $periode);
$smarty->assign("cat_used"        , $cat_used);
$smarty->assign("token_cat"       , $token_cat);
$smarty->assign("cats"            , $cats);
$smarty->assign("dates"           , $dates);
$smarty->assign("categories"      , $categories);
$smarty->assign("prescription"    , $prescription);
$smarty->assign("sejours"         , $sejours);
$smarty->assign("lines_by_patient", $lines_by_patient);
$smarty->assign("list_lines"      , $list_lines);
$smarty->assign("lits"            , $lits);
$smarty->assign("dateTime_min"    , $dateTime_min);
$smarty->assign("dateTime_max"    , $dateTime_max);
$smarty->assign("cat_groups"      , $cat_groups);
$smarty->assign("all_groups"      , $all_groups);
$smarty->assign("by_patient"      , $by_patient);
$smarty->assign("show_inactive"   , $show_inactive);
$smarty->assign("_present_only"   , $_present_only);
$smarty->assign("offline"         , $offline);
$smarty->assign("cat_group_id"    , CValue::get("cat_group_id"));
$smarty->assign("params"          , CConstantesMedicales::$list_constantes);
$smarty->display('vw_bilan_service.tpl');

?>