<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionLineHandler extends CMbObjectHandler {
  static $handled = array ("CSejour","COperation");
  
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
    //$where[] = "jour_decalage = '$param' OR jour_decalage_fin = '$param'";
    $where[] = "jour_decalage IS NOT NULL OR jour_decalage_fin IS NOT NULL";
    
    $line_medicament = new CPrescriptionLineMedicament();
    $line_element = new CPrescriptionLineElement();
    $lines = array();
    $lines["med"] = $line_medicament->loadList($where);
    $lines["elt"] = $line_element->loadList($where);
    
    $perfusion = new CPerfusion();
    $wherePerf = array();
    $wherePerf["prescription_id"] = " = '$prescription_sejour_id'";
    $wherePerf["decalage_interv"] = "IS NOT NULL";
    $lines["perf"] = $perfusion->loadList($wherePerf);
    return $lines;
  }
  
  
  function onStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    
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
    
    
    
   // On charge toutes les lignes qui sont définies en fonction de l'entree du sejour
   if($mbObject->_class_name == "COperation"){
     $mbObject->loadRefSejour();
     $sejour =& $mbObject->_ref_sejour;
   } else {
     $sejour =& $mbObject; 
   }  
              
   $lines = $this->getLines($sejour);
     
   foreach($lines as $type => $lines_by_type){
     if($type == "med" || $type == "elt") {
	     foreach($lines_by_type as $_line){
	       if(!$_line->decalage_line){
	         $_line->decalage_line = 0;
	       }
	       if(!$_line->decalage_line_fin){
	         $_line->decalage_line_fin = 0;
	       }
	       
	       if($_line->jour_decalage == "E"){  
	         $signe = ($_line->decalage_line >= 0) ? "+" : "";
	 	       $_line->debut = mbDate("$signe $_line->decalage_line DAYS", $sejour->_entree);
	       } 
	       if($_line->jour_decalage == "S"){
	         $signe_debut = ($_line->decalage_line >= 0) ? "+" : "";
	 	       $_line->debut = mbDate("$signe_debut $_line->decalage_line DAYS", mbDate($sejour->_sortie));	
	       }    
	       // modification de la fin
	       if($_line->jour_decalage_fin == "S"){
	         $signe_fin = ($_line->decalage_line_fin >= 0) ? "+" : "";
	 	       $date_fin = mbDate("$signe_fin $_line->decalage_line_fin DAYS", mbDate($sejour->_sortie));   	
	         $_line->duree = mbDaysRelative($_line->debut, $date_fin);
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
	  	            //$_line->debut = $date_operation;
	                //$_line->time_debut = mbTime("$signe_debut $_line->decalage_line HOURS", $hour_operation);   
	                $date_time_debut = mbDateTime("$signe_debut $_line->decalage_line HOURS", "$date_operation $hour_operation");
	                $_line->debut = mbDate($date_time_debut);
	                $_line->time_debut = mbTime($date_time_debut);
	              } else {
	                $_line->debut = mbDate("$signe_debut $_line->decalage_line DAYS", $date_operation); 
	              }
	            }
	          
	            // modification de la fin
	            if($_line->jour_decalage_fin == "I"){
	              $signe_fin = ($_line->decalage_line_fin >= 0) ? "+" : "";
	              if($_line->unite_decalage_fin == "heure"){
	                //$date_fin = $date_operation;
	                //$_line->time_fin = mbTime("$signe_fin $_line->decalage_line_fin HOURS", $hour_operation);   
	                $date_time_fin = mbDateTime("$signe_fin $_line->decalage_line_fin HOURS", "$date_operation $hour_operation");
	                $date_fin = mbDate($date_time_fin);
	                $_line->time_fin = mbTime($date_time_fin);
	              } else {
	                $date_fin = mbDate("$signe_fin $_line->decalage_line_fin DAYS", $date_operation);
	              }
	              $_line->duree = mbDaysRelative($_line->debut, $date_fin);
	              $_line->duree++;
	            }
	          }
		        if($_line->duree < 0){
		          $_line->duree = 1;
		        }
		        
		        $_line->loadRefsPrises();
		        foreach($_line->_ref_prises as &$_prise){
				      if($_prise->decalage_intervention != NULL){
								$signe_decalage_intervention = ($_prise->decalage_intervention >= 0) ? "+" : "";
							  $_prise->heure_prise = mbTime("$signe_decalage_intervention $_prise->decalage_intervention HOURS", $hour_operation);	  
							  $_prise->store();
				      }
		        }
	        $_line->store();
	      }
	    }
    }
    if($type == "perf"){
      foreach($lines_by_type as $_perfusion){
	      if($_perfusion->operation_id){
	        $operation = new COperation();
	        $operation->load($_perfusion->operation_id);
	        $operation->loadRefPlageOp();
	      } else {     
	        if($mbObject->_class_name == "COperation"){
	          $_perfusion->operation_id = $mbObject->_id;
	          $operation =& $mbObject;
	          $operation->loadRefPlageOp();
	        } else {
	          continue;
	        }            
	      }
	      
	      // Si la ligne ne possede pas d'operation_id et qu'on manipule une operation, on lui affecte l'id de l'operation          
		    $hour_operation = $operation->fin_op ? $operation->fin_op : ($operation->debut_op ? $operation->debut_op : $operation->time_operation);
		    $date_operation = $operation->_ref_plageop->date;
	      
		    if($_perfusion->decalage_interv == ""){
		  	  $_perfusion->decalage_interv = 0;
		  	}
		  	$signe = ($_perfusion->decalage_interv >= 0) ? "+" : "";
		    $date_time_debut = mbDateTime("$signe $_perfusion->decalage_interv HOURS", "$date_operation $hour_operation");
		  	$_perfusion->date_debut = mbDate($date_time_debut);
		  	$_perfusion->time_debut = mbTime($date_time_debut);
		  	
		  	 $_perfusion->store();
	    }
    }
  }
  
  function onMerge(CMbObject &$mbObject) {
    $this->onStore($mbObject);
  }
  
  function onDelete(CMbObject &$mbObject) {
  }
}

?>