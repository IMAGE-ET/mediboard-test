<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionLineHandler extends CMbObjectHandler {
  static $handled = array ("CSejour","COperation", "CAffectation");
  
  static function isHandled(CMbObject $mbObject) {
    if(!CModule::getActive("dPprescription")){
      return;
    }
    return in_array($mbObject->_class, self::$handled);
  }

  function getLines($sejour, $param = ""){
    // Chargement des prescriptions
    if(!$sejour->_ref_prescriptions){
      $sejour->loadRefsPrescriptions();
    }
    // Recuperation de l'id de prescription de sejour
    if(array_key_exists("sejour", $sejour->_ref_prescriptions)){
      $prescription_sejour_id = $sejour->_ref_prescriptions["sejour"]->_id;
    }
    // Chargement des lignes de medicaments et d'elements définies en fonction de $param
    $whereElt["prescription_id"] = " = '$prescription_sejour_id'";
		$whereElt[] = "jour_decalage IS NOT NULL OR jour_decalage_fin IS NOT NULL";
    
		$whereMed["prescription_id"] = " = '$prescription_sejour_id'";
    $whereMed[] = "jour_decalage IS NOT NULL OR jour_decalage_fin IS NOT NULL OR
		            (jour_decalage IS NULL AND jour_decalage_fin IS NULL AND duree IS NULL)";
    
    $line_medicament = new CPrescriptionLineMedicament();
    $line_element = new CPrescriptionLineElement();
    $lines = array();
    $lines["med"] = $line_medicament->loadList($whereMed);
    $lines["elt"] = $line_element->loadList($whereElt);
    
    $prescription_line_mix = new CPrescriptionLineMix();
    $wherePerf = array();
    $wherePerf["prescription_id"] = " = '$prescription_sejour_id'";
    $wherePerf[] = "jour_decalage IS NOT NULL OR jour_decalage_fin IS NOT NULL";
    $lines["perf"] = $prescription_line_mix->loadList($wherePerf);
    return $lines;
  }
  
  
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
	  
		// On desactive le handler des alertes
    CMbObject::ignoreHandler("CPrescriptionAlerteHandler");
		
    if($mbObject instanceof CSejour){
      if(!$mbObject->fieldModified("entree") && !$mbObject->fieldModified("sortie")){
        return;
      }
			CPlanificationSysteme::$_calcul_planif = false;
    }
    if($mbObject instanceof COperation){
      if(!$mbObject->fieldModified("debut_op") && !$mbObject->fieldModified("fin_op") && !$mbObject->fieldModified("time_operation") && !$mbObject->fieldModified("plageop_id") && !$mbObject->fieldModified("date") && !$mbObject->fieldModified("induction_debut")){       
        return;
      }
    }

    // Attention, l'affectation n'est pas forcement mis a jour avant le sejour (rajouter des tests) !!
    if($mbObject instanceof CAffectation){
    	// Suppression des planifs lors de la sauvegarde d'une affectation
      $prescription = new CPrescription();
      $prescription->object_id = $mbObject->sejour_id;
      $prescription->object_class = "CSejour"; // supprimer l'objet class
      $prescription->type = "sejour"; // renommer sejour en hospitalisation
      $prescription->loadMatchingObject();
      $prescription->removeAllPlanifSysteme();   
      return;
    }
		
   // On charge toutes les lignes qui sont définies en fonction de l'entree du sejour
   if($mbObject->_class == "COperation"){
     $mbObject->loadRefSejour();
     $sejour =& $mbObject->_ref_sejour;
   } else {
     $sejour =& $mbObject; 
   }  
              
   $lines = $this->getLines($sejour);
  
   foreach($lines as $type => $lines_by_type){
     foreach($lines_by_type as $_line){
     	 
			 // Modification des lignes de medicaments non issues d'un protocole
			 if($_line instanceof CPrescriptionLineMedicament && !$_line->jour_decalage && !$_line->jour_decalage_fin){
     	 	 if(!$_line->inscription){
	         $_line->removePlanifSysteme();
	         if(!$_line->substituted && $_line->variante_active){
	         	 $_line->calculPlanifSysteme();
					 }
	       }
				 continue;
			 }
       
			 // Si la ligne a des administrations liées a des planif systemes, on ne passe pas dans le handler
			 if($_line->_count_locked_planif > 0){
			 	 continue;
			 }
			 
       if(!$_line->decalage_line){
         $_line->decalage_line = 0;
       }
       if(!$_line->decalage_line_fin){
         $_line->decalage_line_fin = 0;
       }
       
			 $unite_decalage_debut = $_line->unite_decalage === "heure" ? "HOURS" : "DAYS";
       $unite_decalage_fin   = $_line->unite_decalage_fin === "heure" ? "HOURS" : "DAYS";
		
			 // Modification de la date de debut en fonction de la date d'entree
       if($_line->jour_decalage == "E"){  
         $signe = ($_line->decalage_line >= 0) ? "+" : "";
				 $datetime_debut = mbDateTime("$signe $_line->decalage_line $unite_decalage_debut", $sejour->entree);

				 if($_line instanceof CPrescriptionLineMix){
				   $_line->date_debut = mbDate($datetime_debut);
         } else {
				 	 $_line->debut = mbDate($datetime_debut);
         }
				 if($_line->unite_decalage == "heure"){
				 	 $_line->time_debut = mbTime($datetime_debut);
         }
			 }
				
			 // Modification de la date de fin en fonction de la date de sortie
       if($_line->jour_decalage == "S"){
         $signe_debut = ($_line->decalage_line >= 0) ? "+" : "";
				 $datetime_debut = mbDateTime("$signe_debut $_line->decalage_line $unite_decalage_debut", $sejour->sortie);
				 if($_line instanceof CPrescriptionLineMix){
				   $_line->date_debut = mbDate($datetime_debut);
         } else {
				   $_line->debut = mbDate($datetime_debut);
       	 }
				 if($_line->unite_decalage == "heure"){
				   $_line->time_debut = mbTime($datetime_debut);
				 }
 	     }
			 
			 // Modification de la fin 
			  if($_line->jour_decalage_fin == "S"){
          $signe_fin = ($_line->decalage_line_fin >= 0) ? "+" : "";
          
          if($_line->unite_decalage_fin == "jour"){
            $date_fin = mbDate("$signe_fin $_line->decalage_line_fin DAYS", $sejour->sortie);
            $_debut = ($_line instanceof CPrescriptionLineMix) ? $_line->date_debut : $_line->debut;
            $_line->duree = mbDaysRelative($_debut, $date_fin);
            $_line->duree++;
          } else {
            $date_time_fin = mbDateTime("$signe_fin $_line->decalage_line_fin HOURS", $sejour->sortie);
            $date_fin = mbDate($date_time_fin);
            $time_fin = mbTime($date_time_fin);

            if($_line instanceof CPrescriptionLineMix){
              $duree_hours = mbHoursRelative("$_line->date_debut $_line->time_debut", $date_time_fin);
            } else {
              $duree_hours = mbHoursRelative("$_line->debut $_line->time_debut", $date_time_fin);
            }
    
            if(($duree_hours <= 24) || ($unite_decalage_debut == "HOURS" && $unite_decalage_fin == "HOURS")){
              $_line->unite_duree = "heure";
              $_line->duree = $duree_hours;
            } else {
              $_line->unite_duree = "jour";
              if ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineElement) {
                $_line->duree = mbDaysRelative($_line->debut, $date_fin);
              }
              else {
                $_line->duree = mbDaysRelative($_line->date_debut, $date_fin);
              }
              $_line->duree++;
              if($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineElement){
                $_line->time_fin = $time_fin;
              }
            }
          }
        }

       if($_line->jour_decalage == "I" || $_line->jour_decalage_fin == "I" || $_line->jour_decalage == "A" || $_line->jour_decalage_fin == "A"){
         // Si la ligne possede deja une operation_id 
         if($_line->operation_id){
            $operation = new COperation();
            $operation->load($_line->operation_id);
            $operation->loadRefPlageOp();
          } else {     
            if($mbObject->_class == "COperation"){
              $_line->operation_id = $mbObject->_id;
              $operation =& $mbObject;
              $operation->loadRefPlageOp();
            } else {
              continue;
            }
          }
          
					$date_operation = mbDate($operation->_datetime);

           // modification du debut
           if($_line->jour_decalage == "I" || $_line->jour_decalage == "A"){
             if($_line->jour_decalage == "A"){
	             $hour_operation = $operation->induction_debut ? $operation->induction_debut : mbTime($operation->_datetime);
	           } else {
	             $hour_operation = $operation->fin_op ? $operation->fin_op : ($operation->debut_op ? $operation->debut_op : $operation->time_operation);
	           }
					
					   $signe_debut = ($_line->decalage_line >= 0) ? "+" : "";
             if($_line->unite_decalage == "heure"){  
                $date_time_debut = mbDateTime("$signe_debut $_line->decalage_line HOURS", "$date_operation $hour_operation");
                if($_line instanceof CPrescriptionLineMix){
                	$_line->date_debut = mbDate($date_time_debut);
                } else {
                	$_line->debut = mbDate($date_time_debut);
                }
								$_line->time_debut = mbTime($date_time_debut);
              } else {
              	if($_line instanceof CPrescriptionLineMix){
              		$_line->date_debut = mbDate("$signe_debut $_line->decalage_line DAYS", $date_operation); 
                } else {
              		$_line->debut = mbDate("$signe_debut $_line->decalage_line DAYS", $date_operation); 
                }
              }
            }
         
            // modification de la fin
            if($_line->jour_decalage_fin == "I" || $_line->jour_decalage_fin == "A"){
            	if($_line->jour_decalage_fin == "A"){
	              $hour_operation = $operation->induction_debut ? $operation->induction_debut : mbTime($operation->_datetime);
	            } else {
	              $hour_operation = $operation->fin_op ? $operation->fin_op : ($operation->debut_op ? $operation->debut_op : $operation->time_operation);
	            }
							
              $signe_fin = ($_line->decalage_line_fin >= 0) ? "+" : "";
              
							if($_line->unite_decalage_fin == "jour"){
                $date_fin = mbDate("$signe_fin $_line->decalage_line_fin DAYS", $date_operation);
								$_debut = ($_line instanceof CPrescriptionLineMix) ? $_line->date_debut : $_line->debut;
								$_line->duree = mbDaysRelative($_debut, $date_fin);
                $_line->duree++;
							} else {
							  $date_time_fin = mbDateTime("$signe_fin $_line->decalage_line_fin HOURS", "$date_operation $hour_operation");
                $date_fin = mbDate($date_time_fin);
								$time_fin = mbTime($date_time_fin);

				        if($_line instanceof CPrescriptionLineMix){
				          $duree_hours = mbHoursRelative("$_line->date_debut $_line->time_debut", $date_time_fin);
				        } else {
				          $duree_hours = mbHoursRelative("$_line->debut $_line->time_debut", $date_time_fin);
				        }
				
			          if(($duree_hours <= 24) || ($unite_decalage_debut == "HOURS" && $unite_decalage_fin == "HOURS")){
			            $_line->unite_duree = "heure";
			            $_line->duree = $duree_hours;
			          } else {
			            $_line->unite_duree = "jour";
			            if ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineElement) {
			              $_line->duree = mbDaysRelative($_line->debut, $date_fin);
			            }
			            else {
			              $_line->duree = mbDaysRelative($_line->date_debut, $date_fin);
			            }
			            $_line->duree++;
									if($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineElement){
										$_line->time_fin = $time_fin;
									}
			          }
			        
	            }
						}
					}
	        if($_line->duree < 0){
	          $_line->duree = 1;
	        }
        
				if (!($_line instanceof CPrescriptionLineMix)) {
				  $_line->loadRefsPrises();
	        foreach($_line->_ref_prises as &$_prise){
	        	if($_prise->type_decalage == "A"){
		          $hour_operation = $operation->induction_debut ? $operation->induction_debut : mbTime($operation->_datetime);
		        }
	        	if($_prise->decalage_intervention != NULL){
			      	$_line->_update_planif_systeme = true;
							$signe_decalage_intervention = ($_prise->decalage_intervention >= 0) ? "+" : "";
							$unite_decalage_intervention = ($_prise->unite_decalage_intervention == "heure") ? "HOURS" : "MINUTES";
						  $_prise->heure_prise = mbTime("$signe_decalage_intervention $_prise->decalage_intervention $unite_decalage_intervention", $hour_operation);	  
						  $_prise->store();
			      }
	        }
				}
        $_line->store();
      }
    }
		CPlanificationSysteme::$_calcul_planif = true;
  }
  
  function onAfterMerge(CMbObject $mbObject) {
    $this->onAfterStore($mbObject);
  }
  
  function onAfterDelete(CMbObject $mbObject) {
  	 if (!$this->isHandled($mbObject)) {
      return;
    }
		
		if($mbObject instanceof CAffectation){
      // Suppression des planifs lors de la sauvegarde d'une affectation
      $prescription = new CPrescription();
      $prescription->object_id = $mbObject->sejour_id;
      $prescription->object_class = "CSejour";
      $prescription->type = "sejour";
      $prescription->loadMatchingObject();
      $prescription->removeAllPlanifSysteme();   
      return;
    }
  }
}

?>