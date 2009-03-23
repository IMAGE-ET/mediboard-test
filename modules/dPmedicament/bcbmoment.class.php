<?php /* */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

require_once("bcbObject.class.php");

class CBcbMoment extends CBcbObject {

	var $code_moment_id = null;
	var $libelle_moment = null;
	var $coeff          = null;
	
	var $_ref_associations = null;
	
	
  // Constructeur
  function CBcbMoment(){
  
  }
 
  // Chargement d'une posologie a partir d'un code CIP
  function load($code_moment_id){
    // Chargement des posologies du produit
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM `POSO_MOMENTS` WHERE `CODE_MOMENT` = '$code_moment_id';";  
    $ds->loadObject($query, $moment);
    
    if (!$moment){
      return;
    }

    $this->code_moment_id = $moment->CODE_MOMENT;
    $this->libelle_moment = $moment->LIBELLE_MOMENT;
    $this->coeff          = $moment->COEFF;
		
    $this->updateFormFields();
  }
  
  // Chargement des associations entre moment BCB et moments unitaires
  function loadRefsAssociations(){
  	$association_moment = new CAssociationMoment();
    $association_moment->code_moment_id = $this->code_moment_id;
    $this->_ref_associations = $association_moment->loadMatchingList();
    
    foreach($this->_ref_associations as &$association){
    	$association->loadRefMomentUnitaire();
    }
  }
  
  // Chargement de tous les moments
  static function loadAllMoments(){
    $ds = CBcbObject::getDataSource();
    $query = "SELECT * FROM POSO_MOMENTS";
    $moments = $ds->loadList($query);
    return $moments;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
  }

}

?>
