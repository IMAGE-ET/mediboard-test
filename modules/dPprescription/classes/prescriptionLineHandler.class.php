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
  
  static function isHandled(CMbObject &$mbObject) {
    if(!CModule::getActive("dPprescription")){
      return;
    }
    return in_array($mbObject->_class_name, self::$handled);
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
    $where["prescription_id"] = " = '$prescription_sejour_id'";
    $where[] = "jour_decalage IS NOT NULL OR jour_decalage_fin IS NOT NULL";
    
    $line_medicament = new CPrescriptionLineMedicament();
    $line_element = new CPrescriptionLineElement();
    $lines = array();
    $lines["med"] = $line_medicament->loadList($where);
    $lines["elt"] = $line_element->loadList($where);
    
    $prescription_line_mix = new CPrescriptionLineMix();
    $wherePerf = array();
    $wherePerf["prescription_id"] = " = '$prescription_sejour_id'";
    $wherePerf[] = "jour_decalage IS NOT NULL OR jour_decalage_fin IS NOT NULL";
    $lines["perf"] = $prescription_line_mix->loadList($wherePerf);
    return $lines;
  }
  
  
  function onAfterStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    
		// On desactive le handler des alertes
    CMbObject::ignoreHandler("CPrescriptionAlerteHandler");
		
    if($mbObject instanceof CSejour){
      if(!$mbObject->fieldModified("_entree") && !$mbObject->fieldModified("_sortie")){
        return;
      }
    }
    if($mbObject instanceof COperation){
      if(!$mbObject->fieldModified("debut_op") && !$mbObject->fieldModified("fin_op") && !$mbObject->fieldModified("time_operation") && !$mbObject->fieldModified("plageop_id")){       
        return;
      }
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
		
		
		
   // On charge toutes les lignes qui sont définies en fonction de l'entree du sejour
   if($mbObject->_class_name == "COperation"){
     $mbObject->loadRefSejour();
     $sejour =& $mbObject->_ref_sejour;
   } else {
     $sejour =& $mbObject; 
   }  
              
   $lines = $this->getLines($sejour);
     
   foreach($lines as $type => $lines_by_type){
     foreach($lines_by_type as $_line){
     	 $_line->countLockedPlanif();
			 
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
				 $date_debut =  mbDate("$signe $_line->decalage_line DAYS", $sejour->entree);
				 if($_line instanceof CPrescriptionLineMix){
				   $_line->date_debut = $date_debut;
         } else {
				 	 $_line->debut = $date_debut;
         }
				 $_line->time_debut = mbTime($sejour->entree);
 	     } 
			 // Modification de la date de fin en fonction de la date de sortie
       if($_line->jour_decalage == "S"){
         $signe_debut = ($_line->decalage_line >= 0) ? "+" : "";
				 $date_debut = mbDate("$signe_debut $_line->decalage_line DAYS", mbDate($sejour->sortie));
				 if($_line instanceof CPrescriptionLineMix){
				   $_line->date_debut = $date_debut;  
         } else {
				   $_line->debut = $date_debut; 
       	 }
				 $_line->time_debut = mbTime($sejour->sortie);
 	     }
			 
			 // modification de la fin
       if($_line->jour_decalage_fin == "S"){
       	 $_debut = ($_line instanceof CPrescriptionLineMix) ? $_line->date_debut : $_line->debut;
         $signe_fin = ($_line->decalage_line_fin >= 0) ? "+" : "";
 	       $date_fin = mbDate("$signe_fin $_line->decalage_line_fin DAYS", mbDate($sejour->_sortie));   	
         $_line->duree = mbDaysRelative($_debut, $date_fin);
         $_line->duree++;
       }
			 
       if($_line->jour_decalage == "I" || $_line->jour_decalage_fin == "I"){
         // Si la ligne possede deja une operation_id 
         if($_line->operation_id){
            $operation = new COperation();
            $operation->load($_line->operation_id);
            $operation->loadRefPlageOp();
          } else {     
            if($mbObject->_class_name == "COperation"){
              $_line->operation_id = $mbObject->_id;
              $operation =& $mbObject;
              $operation->loadRefPlageOp();
            } else {
              continue;
            }
          }  
          // Si la ligne ne possede pas d'operation_id et qu'on manipule une operation, on lui affecte l'id de l'operation          
          $hour_operation = $operation->fin_op ? $operation->fin_op : ($operation->debut_op ? $operation->debut_op : $operation->time_operation);
          $date_operation = $operation->_ref_plageop->date;

          // modification du debut
          if($_line->jour_decalage == "I"){
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
            if($_line->jour_decalage_fin == "I"){
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
				          $duree_hours_mix = mbHoursRelative("$_line->date_debut $_line->time_debut", $date_time_fin);
				
				          if(($duree_hours_mix <= 24) || ($unite_decalage_debut == "HOURS" && $unite_decalage_fin == "HOURS")){
				            $_line->unite_duree = "heure";
				            $_line->duree = $duree_hours_mix;
				          } else {
				            $_line->unite_duree = "jour";
				            $_line->duree = mbDaysRelative($_line->date_debut, $date_fin);
				            $_line->duree++;
				          }
				        } else {
				          $_line->duree = mbDaysRelative($_line->debut, $date_fin);
				          $_line->duree++;
								  $_line->time_fin = $time_fin;
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
  }
  
  function onAfterMerge(CMbObject &$mbObject) {
    $this->onAfterStore($mbObject);
  }
  
  function onAfterDelete(CMbObject &$mbObject) {
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