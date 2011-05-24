<?php /* $Id: prescriptionLineHandler.class.php 11723 2011-04-01 09:51:18Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 11723 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionAlerteHandler extends CMbObjectHandler {
  static $handled = array ("CPrisePosologie", 
	                         "CPrescriptionLineMedicament", 
													 "CPrescriptionLineElement", 
													 "CPrescriptionLineComment", 
													 "CPrescriptionLineMix", 
													 "CPrescriptionLineMixItem");
  
  static function isHandled(CMbObject &$mbObject) {
    if(!CModule::getActive("dPprescription")){
      return;
    }
    return in_array($mbObject->_class_name, self::$handled);
  }

  /*
   * Retourne l'object concerné par l'alerte
   */
  function getAlerteObject(CMbObject &$mbObject){
  	if($mbObject instanceof CPrisePosologie){
			$mbObject->loadTargetObject();
      $object = $mbObject->_ref_object;
		}
    if($mbObject instanceof CPrescriptionLineMixItem){
    	if(!$mbObject->prescription_line_mix_id){
    		// Suppression d'une line_mix_item
				$object = $mbObject->_old->_ref_prescription_line_mix;
    	} else {
        $object = $mbObject->_ref_prescription_line_mix;
    	}
    }
    if(!isset($object)){
      $object = $mbObject;      
    }
		return $object;
  }
	
	
  /*
   * Chargement de l'alerte liée à la ligne de prescription sinon creation d'une alerte
   */
  function updateAlerte(CMbObject &$mbObject){
  	// Chargement de l'alerte
    $alerte = new CAlert();
		
    $object = $this->getAlerteObject($mbObject);
    if($object->_protocole){
    	return;
    }
		
    $alerte->setObject($object);
    $alerte->tag = "prescription_modification";
    $alerte->loadMatchingObject();
		
		// Si la ligne (substitution) n'est pas active, on ne genere pas d'alerte (on la supprime si elle existe)
		if(($object instanceof CPrescriptionLineMedicament || $object instanceof CPrescriptionLineMix)){
			// Suppression de l'alerte si elle existe
			if(!$object->substitution_active){
		    if($alerte->_id){
		    	$alerte->delete();
		    }
				return;
			}
		}
		 
		if($object instanceof CPrescriptionLineMedicament || $object instanceof CPrescriptionLineElement){
			// Si les _ref_lines sont deja calculés (cas de l'application du protocole), ne pas les vider
			if(!$object->_ref_prises){
				$object->loadRefsPrises();
      }
			$_poso_views = array();
	    foreach ($object->_ref_prises as $_poso){
	    	$_poso->loadRefsFwd();
	      $_poso_views[] = $_poso->_view;
	    }
	    $_poso_views = implode(", ", $_poso_views);
	    
			$alerte->comments = "$object->_view - $object->_duree_prise : \n$_poso_views";
		}

    if($object instanceof CPrescriptionLineComment){
      $alerte->comments = $object->_view;
    }
		
    if($object instanceof CPrescriptionLineMix){
			// Si les _ref_lines sont deja calculés (cas de l'application du protocole), ne pas les vider
			if(!$object->_ref_lines){
				$object->loadRefsLines();
      }
		  $alerte->comments = "$object->_view, $object->_short_view";
    }

    $alerte->handled = 0;
		if($mbObject instanceof CPrisePosologie && $mbObject->urgence_datetime){
      $alerte->level = "high";
    }
		
    $alerte->store(); 
  }
 
  function onAfterStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    $this->updateAlerte($mbObject);
  }
  
  function onAfterMerge(CMbObject &$mbObject) {
    $this->onAfterStore($mbObject);
  }
  
  function onAfterDelete(CMbObject &$mbObject) {
     if (!$this->isHandled($mbObject)) {
      return;
    }
		if($mbObject instanceof CPrisePosologie || $mbObject instanceof CPrescriptionLineMixItem){
      $this->updateAlerte($mbObject);
		}
		
		if($mbObject instanceof CPrescriptionLineMedicament || $mbObject instanceof CPrescriptionLineMix){
		  $alerte = new CAlert();
	    $alerte->setObject($mbObject->_old);
	    $alerte->tag = "prescription_modification";
	    $alerte->loadMatchingObject();
      if($alerte->_id){
      	$alerte->delete();
      }
		}
	}
}

?>