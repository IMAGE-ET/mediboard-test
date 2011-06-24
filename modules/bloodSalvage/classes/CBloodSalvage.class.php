<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * The blood salvage Class. 
 * This class registers informations about an intraoperative blood salvage operation.
 * A blood salvage operation is referenced to an operation (@param $_ref_operation_id) 
 */

class CBloodSalvage extends CMbObject {
	//DB Table Key
	var $blood_salvage_id = null;
	
	//DB References 
	var $operation_id = null;
	var $cell_saver_id = null;     // The Cell Saver equipment
	var $type_ei_id = null;        // Reference to an incident type
	
	//DB Fields
	var $wash_volume = null;       // *Volume de lavage*
	var $saved_volume = null;      // *Volume récupéré pendant la manipulation*
	var $hgb_pocket = null;        // *Hémoglobine de la poche récupérée*
	var $hgb_patient = null;       // *Hémoglobine du patient post transfusion*
	var $transfused_volume = null;
	var $anticoagulant_cip = null; // *Code CIP de l'anticoagulant utilisé*
	
	var $receive_kit_ref = null;
	var $receive_kit_lot = null;
	
	var $wash_kit_ref    = null;
  var $wash_kit_lot    = null;
  
	var $sample = null;
	
  // Form Fields
  var $_recuperation_start =null;
  var $_recuperation_end =null;
  var $_transfusion_start = null;
  var $_transfusion_end = null;
	 
  //Distants Fields
  var $_datetime = null;
  
	//Timers for the operation
	var $recuperation_start = null;
	var $recuperation_end = null;
	var $transfusion_start = null;
	var $transfusion_end = null;
	
	//Refs
	var $_ref_operation = null;
	var $_ref_cell_saver = null;
	var $_ref_incident_type = null;
  var $_ref_patient = null;
	
	function getSpec() {
		$spec = parent::getSpec();
    $spec->table = 'blood_salvage';
    $spec->key   = 'blood_salvage_id';
    return $spec;
	}
	/*
	 * Spécifications. Indique les formats des différents éléments et références de la classe.
	 */
	function getProps() {
		$specs= parent::getProps();
		$specs["operation_id"]				= "ref notNull class|COperation";
		$specs["cell_saver_id"]				= "ref class|CCellSaver";
	  $specs["type_ei_id"]		      = "ref class|CTypeEi";
	  
    $specs["recuperation_start"]  = "dateTime";
    $specs["recuperation_end"]    = "dateTime";
    $specs["transfusion_start"]   = "dateTime";
    $specs["transfusion_end"]     = "dateTime";
   
    $specs["_recuperation_start"]  = "time";
    $specs["_recuperation_end"]    = "time";
    $specs["_transfusion_start"]   = "time";
    $specs["_transfusion_end"]     = "time";
    
    $specs["wash_volume"]					= "num";
    $specs["saved_volume"]				= "num";
    $specs["transfused_volume"]   = "num";
    $specs["hgb_pocket"]					= "num";
    $specs["hgb_patient"]					= "num";
    $specs["anticoagulant_cip"]		= "numchar length|7";
    $specs["wash_kit_ref"]	      = "str maxLength|32 autocomplete";
    $specs["wash_kit_lot"]        = "str maxLength|32";
    $specs["receive_kit_ref"]     = "str maxLength|32 autocomplete";
    $specs["receive_kit_lot"]	    = "str maxLength|32";
    $specs["sample"]	       	    = "enum notNull list|non|prel|trans default|non";
    
    $specs["_datetime"]           = "dateTime";
    
    return $specs;
	}

  function loadRefsFwd() { 	
  	$this->loadRefOperation();
    $this->loadRefPatient();
    $this->loadRefCellSaver(); 
    $this->loadRefTypeEi();
    $this->_view = "RSPO de {$this->_ref_patient->_view}";
  }
  
  function loadRefPatient() {
    $this->_ref_patient = new CPatient();
    $this->_ref_operation->loadRefPatient(1);
    $this->_ref_patient = $this->_ref_operation->_ref_patient;
  }
  
  function loadRefOperation() {
    $this->_ref_operation = new COperation();
    $this->_ref_operation = $this->_ref_operation->getCached($this->operation_id);
    $this->_ref_operation->loadRefPlageOp(1);
  }
  
  function loadRefCellSaver() {
  	$this->_ref_cell_saver = new CCellSaver();
    $this->_ref_cell_saver = $this->_ref_cell_saver->getCached($this->cell_saver_id);  
  }
  
  function loadRefTypeEi() {
  	$this->_ref_incident_type = new CTypeEi();
    $this->_ref_incident_type = $this->_ref_incident_type->getCached($this->type_ei_id);
  }
  
  function loadRefPlageOp() {
    $this->_ref_operation = new COperation;
    $this->_ref_operation = $this->_ref_operation->getCached($this->operation_id);
    $this->_ref_operation->loadRefPlageOp(1);
    $this->_datetime = $this->_ref_operation->_datetime;
  }
	/*
	 * Mise à jour des champs des formulaires (affichage des dateTime en time).
	 */
	
	function updateFormFields() {		
		if($this->recuperation_start){
	    $this->_recuperation_start = mbTime($this->recuperation_start);
	  }
		if($this->recuperation_end){
	    $this->_recuperation_end = mbTime($this->recuperation_end);
	  }
		if($this->transfusion_start){
	    $this->_transfusion_start = mbTime($this->transfusion_start);
	  }
		if($this->transfusion_end){
	    $this->_transfusion_end = mbTime($this->transfusion_end);
	  }
	}
	
	function updateDBFields() {
		
		$this->loadRefPlageOp();
		
    if($this->_recuperation_start =="current") {
      $this->_recuperation_start = mbTime();
    }
    if($this->_recuperation_end =="current") {
      $this->_recuperation_end = mbTime();
    }
    if($this->_transfusion_start =="current") {
      $this->_transfusion_start = mbTime();
    }
    if($this->_transfusion_end =="current") {
      $this->_transfusion_end = mbTime();
    }
		
		if($this->_recuperation_start !== null && $this->_recuperation_start !="") {
			$this->_recuperation_start = mbTime($this->_recuperation_start);
			$this->recuperation_start = mbAddDateTime($this->_recuperation_start, mbDate($this->_datetime));
		}
	  if($this->_recuperation_start === ""){
        $this->recuperation_start= "";
    }
		if($this->_recuperation_end !== null && $this->_recuperation_end !="") {
			$this->_recuperation_end = mbTime($this->_recuperation_end);
			$this->recuperation_end = mbAddDateTime($this->_recuperation_end, mbDate($this->_datetime));
		}
	  if($this->_recuperation_end === ""){
        $this->recuperation_end= "";
    }
		if($this->_transfusion_start !== null && $this->_transfusion_start !="") {
			$this->_transfusion_start = mbTime($this->_transfusion_start);
			$this->transfusion_start = mbAddDateTime($this->_transfusion_start, mbDate($this->_datetime));
		}
	  if($this->_transfusion_start === ""){
        $this->transfusion_start= "";
    }
		if($this->_transfusion_end !== null && $this->_transfusion_end !="") {
			$this->_transfusion_end = mbTime($this->_transfusion_end);
			$this->transfusion_end = mbAddDateTime($this->_transfusion_end, mbDate($this->_datetime));
		}
	  if($this->_transfusion_end === ""){
        $this->transfusion_end= "";
    }
	}
	
	/*
	 * fillTemplate permet de donner des champs qui seront disponibles dans FCK Editor
	 */
	function fillTemplate(&$template) {
		$this->fillLimitedTemplate($template);
	}
	
	function fillLimitedTemplate(&$template) {
        $this->loadRefCellSaver();
        $this->loadRefTypeEi();
    
		$template->addProperty("Cell Saver - Appareil utilisé"                    , $this->_ref_cell_saver->_view);
		$template->addDateTimeProperty("Cell Saver - Début de récupération"       , $this->recuperation_start);
		$template->addDateTimeProperty("Cell Saver - Fin de récupération"         , $this->recuperation_end);
		$template->addDateTimeProperty("Cell Saver - Début de retransfusion"      , $this->transfusion_start);
		$template->addDateTimeProperty("Cell Saver - Début de retransfusion"      , $this->transfusion_end);		
		$template->addProperty("Cell Saver - Volume récupéré"                     , $this->saved_volume." ml");		
		$template->addProperty("Cell Saver - Volume de lavage"                    , $this->wash_volume." ml");		
    $template->addProperty("Cell Saver - Volume retransfusé"                  , $this->transfused_volume." ml");   		
		$template->addProperty("Cell Saver - Hémoglobine de la poche"             , $this->hgb_pocket." g/dl");		
		$template->addProperty("Cell Saver - Hémoglobine patient post-transfusion", $this->hgb_patient." g/dl");		
		if($this->_ref_incident_type->_view) {
			$template->addProperty("Cell Saver - Incident transfusionnel", $this->_ref_incident_type->_view);    
		} else {
	  	$template->addProperty("Cell Saver - Incident transfusionnel", "Aucun incident signalé");    
		}
	}
}
?>