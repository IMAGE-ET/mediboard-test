<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = mbGetValueFromGetOrSession("service_id");
$date = mbGetValueFromGetOrSession("date_pancarte", mbDate());
$lines = array();
$tab = array();

// Chargement du service
$service = new CService();
$service->load($service_id);

// Chargement de la liste des services
$services = $service->loadGroupList();
$patients = array();
$alertes = array();
$perfs = array();
$new = array();

// Chargement des prescriptions qui sont dans le service selectionné
$prescription = new CPrescription();
$prescriptions = array();
$ljoin = array();
$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id";
$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["lit"]      = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]  = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]  = "service.service_id = chambre.service_id";
$where["prescription.object_class"] = " = 'CSejour'";
$where["prescription.type"] = " = 'sejour'";
$where["service.service_id"]  = " = '$service_id'";
$where["sejour.entree_prevue"] = " < '$date 23:59:59'";
$where["sejour.sortie_prevue"] = " > '$date 00:00:00'";
$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);

if(!$service_id){
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
  $date_min = mbDate("- 1 DAY", $date)." ".reset($soir).":00:00";
  $dates = array($date => array("matin" => $matin, "soir" => $soir));
}
if(in_array($time, $soir)){
  $date_min = mbDate("- 1 DAY", $date)." ".reset($nuit).":00:00"; 
  $dates = array($date => array("soir" => $soir, "nuit" => $nuit));
}
if(in_array($time, $nuit)){
  $date_min = mbDate("- 1 DAY", $date)." ".reset($matin).":00:00"; 
  $dates = array($date => array("nuit" => $nuit), mbDate("+ 1 DAY", $date) => array("matin" => $matin));
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

$lits = array();

// Calcul du plan de soin pour chaque prescription
foreach($prescriptions as $_prescription){
  $_prescription->_ref_object->loadRefsAffectations();
  $lits[$_prescription->_ref_object->_ref_last_affectation->_ref_lit->_view."-".$_prescription->_id] = $_prescription->_id;
  
  $patients[$_prescription->_ref_patient->_id] = $_prescription->_ref_patient;
  $_prescription->loadRefPraticien();
  $_prescription->_ref_praticien->loadRefFunction();
  $_prescription->_ref_patient->loadRefPhotoIdentite();
  $_prescription->loadRefsLinesMedByCat("1","1","service"); 
  $_prescription->loadRefsPerfusions();
  $_prescription->loadRefsLinesElementByCat("1",null,"service");
			
  foreach($tabHours as $curr_date => $curr_hours) {
    $_prescription->calculPlanSoin($curr_date);
  }

  // Creation du tableau de stockage des elements precrits pour un patient et un dateTime donné
	foreach($tabHours as $_date => $_hours_by_moment){
    foreach($_hours_by_moment as $moment_journee => $_dates){
      foreach($_dates as $date_reelle => $_hours){
        foreach($_hours as $_heure_reelle => $_hour){
          $dateTime = "$date_reelle $_heure_reelle";

          // Parcours des medicaments
          if($_prescription->_ref_lines_med_for_plan){
	          foreach($_prescription->_ref_lines_med_for_plan as $_cat_ATC){
					    foreach($_cat_ATC as $_line){
					      foreach($_line as $unite_prise => $_line_med){
					        $quantite_prevue = $quantite_adm = 0;
                  if(isset($_line_med->_administrations[$unite_prise][$date_reelle][$_hour]['quantite_planifiee'])){
		                $quantite_prevue = $_line_med->_administrations[$unite_prise][$date_reelle][$_hour]['quantite_planifiee'];
                  } else {
						        if(isset($_line_med->_quantity_by_date[$unite_prise][$date_reelle]['quantites'][$_hour]['total'])){
						          $quantite_prevue = $_line_med->_quantity_by_date[$unite_prise][$date_reelle]['quantites'][$_hour]['total'];
						        }
					        }
					        if($quantite_prevue){
                    @$tab[$_prescription->_id][$dateTime]["med"][$_line_med->_id]["prevue"] = $quantite_prevue;
						      }
					      	if(isset($_line_med->_administrations[$unite_prise][$date_reelle][$_hour]["quantite"])){
						        $quantite_adm = $_line_med->_administrations[$unite_prise][$date_reelle][$_hour]["quantite"];
						        @$tab[$_prescription->_id][$dateTime]["med"][$_line_med->_id]["adm"] = $quantite_adm;
							    }
							    if($quantite_prevue || $quantite_adm){
							      $lines["med"][$_line_med->_id] = $_line_med;
								    if($_line_med->_recent_modification){
						          $new[$_prescription->_id][$dateTime] = 1;
						          @$tab[$_prescription->_id][$dateTime]["med"][$_line_med->_id]["new"] = 1;
						        }
							    }
						      if($quantite_prevue != $quantite_adm){
						        $alertes[$_prescription->_id][$dateTime]["med"] = 1;
						      }
					      }
					    }
	          }
          }
          // Parcours des injections
          if($_prescription->_ref_injections_for_plan){
	          foreach($_prescription->_ref_injections_for_plan as $_inj_cat_ATC){
					    foreach($_inj_cat_ATC as $_injs){
					      foreach($_injs as $unite_prise => $_line_inj){
					        $quantite_prevue = $quantite_adm = 0;
					        if(isset($_line_inj->_administrations[$unite_prise][$date_reelle][$_hour]['quantite_planifiee'])){
		                $quantite_prevue = $_line_inj->_administrations[$unite_prise][$date_reelle][$_hour]['quantite_planifiee'];
                  } else {
						        if(isset($_line_inj->_quantity_by_date[$unite_prise][$date_reelle]['quantites'][$_hour]['total'])){
						          $quantite_prevue = $_line_inj->_quantity_by_date[$unite_prise][$date_reelle]['quantites'][$_hour]['total'];
						        }
                  }
                  if($quantite_prevue){
					          @$tab[$_prescription->_id][$dateTime]["inj"][$_line_inj->_id]["prevue"] = $quantite_prevue;
					        }
					        if(isset($_line_inj->_administrations[$unite_prise][$date_reelle][$_hour]["quantite"])){
						        $quantite_adm = $_line_inj->_administrations[$unite_prise][$date_reelle][$_hour]["quantite"];
						        @$tab[$_prescription->_id][$dateTime]["inj"][$_line_inj->_id]["adm"] = $quantite_adm;
							    }
							    if($quantite_prevue || $quantite_adm){
							      $lines["inj"][$_line_inj->_id] = $_line_inj;
							      if($_line_inj->_recent_modification){
						          $new[$_prescription->_id][$dateTime] = 1;
						          @$tab[$_prescription->_id][$dateTime]["inj"][$_line_inj->_id]["new"] = 1;
						        }
				          }
						      if($quantite_prevue != $quantite_adm){
						        $alertes[$_prescription->_id][$dateTime]["inj"] = 1;
						      }
					      }
					    }
	          }
          }
          // Parcours des elements
          if($_prescription->_ref_lines_elt_for_plan){
	          foreach($_prescription->_ref_lines_elt_for_plan as $chapitre => $_elements_chap){
	           // $alertes[$_prescription->_id][$dateTime][$chapitre] = false;
	            foreach($_elements_chap as $cat => $_elements_cat){
	              foreach($_elements_cat as $element_id => $_elements){
	                foreach($_elements as $unite_prise => $_line_element){
	                  $quantite_prevue = $quantite_adm = 0;
	                  if(isset($_line_element->_administrations[$unite_prise][$date_reelle][$_hour]['quantite_planifiee'])){
		                  $quantite_prevue = $_line_element->_administrations[$unite_prise][$date_reelle][$_hour]['quantite_planifiee'];
                    } else {
	                    if(isset($_line_element->_quantity_by_date[$unite_prise][$date_reelle]['quantites'][$_hour]['total'])){
						            $quantite_prevue = $_line_element->_quantity_by_date[$unite_prise][$date_reelle]['quantites'][$_hour]['total'];
	                    }
                    }
                    if(@$quantite_prevue){
							        @$tab[$_prescription->_id][$dateTime][$chapitre][$_line_element->_id]["prevue"] = $quantite_prevue;
						        }
	                  if(isset($_line_element->_administrations[$unite_prise][$date_reelle][$_hour]["quantite"])){
						          $quantite_adm = $_line_element->_administrations[$unite_prise][$date_reelle][$_hour]["quantite"];
						          @$tab[$_prescription->_id][$dateTime][$chapitre][$_line_element->_id]["adm"] = $quantite_adm;
							      }
							      if($quantite_prevue || $quantite_adm){
							        $lines[$chapitre][$_line_element->_id] = $_line_element;
							      	if($_line_element->_recent_modification){
							          $new[$_prescription->_id][$dateTime] = 1;
							          @$tab[$_prescription->_id][$dateTime][$chapitre][$_line_element->_id]["new"] = 1;
							        }
							      }
							      if($quantite_prevue != $quantite_adm){
							        $alertes[$_prescription->_id][$dateTime][$chapitre] = 1;
							      }
	                }
	              }
	            }
	          }
          }
          // Parcours des perfusions
          if($_prescription->_ref_perfusions_for_plan){
            foreach($_prescription->_ref_perfusions_for_plan as $_perfusion){
              $_perfusion->loadRefsLines();
              $debut_perf = $_perfusion->_debut ? mbTransformTime(null, $_perfusion->_debut, "%Y-%m-%d %H:00:00") : '';
              $fin_perf = $_perfusion->_fin ? mbTransformTime(null, $_perfusion->_fin, "%Y-%m-%d %H:00:00") : '';
              $debut_perf_adm = $_perfusion->_debut_adm ? mbTransformTime(null, $_perfusion->_debut_adm, "%Y-%m-%d %H:00:00") : '';
              $fin_perf_adm = $_perfusion->_fin_adm ? mbTransformTime(null, $_perfusion->_fin_adm, "%Y-%m-%d %H:00:00") : '';
              if(($debut_perf == $dateTime) || ($fin_perf == $dateTime)){
                @$tab[$_prescription->_id][$dateTime]["perf"][$_perfusion->_id] = $_perfusion;
              	if($_perfusion->_recent_modification){
							    $new[$_prescription->_id][$dateTime] = 1;
							  }
                if($debut_perf == $dateTime){
                  if($debut_perf != $debut_perf_adm){
                    $alertes[$_prescription->_id][$dateTime]["perf"] = 1;    
                  }
                }
                if($fin_perf == $dateTime){
                  if($fin_perf != $fin_perf_adm){
                    $alertes[$_prescription->_id][$dateTime]["perf"] = 1;
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

// Classement par lit
ksort($lits);
$_prescriptions = array();
foreach($lits as $prescription_id){
  $_prescriptions[$prescription_id] = $prescriptions[$prescription_id];
}


// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("tab", $tab);
$smarty->assign("lines", $lines);
$smarty->assign("count_matin", count($matin));
$smarty->assign("count_soir", count($soir));
$smarty->assign("count_nuit", count($nuit));
$smarty->assign("tabHours", $tabHours);
$smarty->assign("service_id", $service_id);
$smarty->assign("services", $services);
$smarty->assign("prescriptions", $_prescriptions);
$smarty->assign("date"     , $date);
$smarty->assign("date_min", $date_min);
$smarty->assign("service", $service);
$smarty->assign("patients", $patients);
$smarty->assign("alertes", $alertes);
$smarty->assign("new", $new);
$smarty->display('vw_pancarte_service.tpl');

?>