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
  public $blood_salvage_id;
  
  //DB References 
  public $operation_id;
  public $cell_saver_id;     // The Cell Saver equipment
  public $type_ei_id;        // Reference to an incident type
  
  //DB Fields
  public $wash_volume;       // *Volume de lavage*
  public $saved_volume;      // *Volume récupéré pendant la manipulation*
  public $hgb_pocket;        // *Hémoglobine de la poche récupérée*
  public $hgb_patient;       // *Hémoglobine du patient post transfusion*
  public $transfused_volume;
  public $anticoagulant_cip; // *Code CIP de l'anticoagulant utilisé*
  
  public $receive_kit_ref;
  public $receive_kit_lot;
  
  public $wash_kit_ref;
  public $wash_kit_lot;
  
  public $sample;
  
  // Form Fields
  public $_recuperation_start;
  public $_recuperation_end;
  public $_transfusion_start;
  public $_transfusion_end;
   
  //Distants Fields
  public $_datetime;
  
  //Timers for the operation
  public $recuperation_start;
  public $recuperation_end;
  public $transfusion_start;
  public $transfusion_end;
  
  /** @var COperation */
  public $_ref_operation;

  /** @var CCellSaver */
  public $_ref_cell_saver;

  /** @var CTypeEi */
  public $_ref_incident_type;

  /** @var CPatient */
  public $_ref_patient;
  
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
    $props = parent::getProps();
    $props["operation_id"]        = "ref notNull class|COperation";
    $props["cell_saver_id"]       = "ref class|CCellSaver";
    $props["type_ei_id"]          = "ref class|CTypeEi";
    
    $props["recuperation_start"]  = "dateTime";
    $props["recuperation_end"]    = "dateTime";
    $props["transfusion_start"]   = "dateTime";
    $props["transfusion_end"]     = "dateTime";
   
    $props["_recuperation_start"] = "time";
    $props["_recuperation_end"]   = "time";
    $props["_transfusion_start"]  = "time";
    $props["_transfusion_end"]    = "time";
    
    $props["wash_volume"]         = "num";
    $props["saved_volume"]        = "num";
    $props["transfused_volume"]   = "num";
    $props["hgb_pocket"]          = "num";
    $props["hgb_patient"]         = "num";
    $props["anticoagulant_cip"]   = "numchar length|7";
    $props["wash_kit_ref"]        = "str maxLength|32 autocomplete";
    $props["wash_kit_lot"]        = "str maxLength|32";
    $props["receive_kit_ref"]     = "str maxLength|32 autocomplete";
    $props["receive_kit_lot"]     = "str maxLength|32";
    $props["sample"]              = "enum notNull list|non|prel|trans default|non";
    
    $props["_datetime"]           = "dateTime";
    
    return $props;
  }

  function loadRefsFwd() {
    $this->loadRefOperation();
    $this->loadRefPatient();
    $this->loadRefCellSaver(); 
    $this->loadRefTypeEi();
    $this->_view = "RSPO de {$this->_ref_patient->_view}";
  }
  
  function loadRefPatient() {
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
    if ($this->recuperation_start) {
      $this->_recuperation_start = CMbDT::time($this->recuperation_start);
    }
    if ($this->recuperation_end) {
      $this->_recuperation_end = CMbDT::time($this->recuperation_end);
    }
    if ($this->transfusion_start) {
      $this->_transfusion_start = CMbDT::time($this->transfusion_start);
    }
    if ($this->transfusion_end) {
      $this->_transfusion_end = CMbDT::time($this->transfusion_end);
    }
  }
  
  function updatePlainFields() {
    $this->loadRefPlageOp();
    
    if ($this->_recuperation_start =="current") {
      $this->_recuperation_start = CMbDT::time();
    }
    if ($this->_recuperation_end =="current") {
      $this->_recuperation_end = CMbDT::time();
    }
    if ($this->_transfusion_start =="current") {
      $this->_transfusion_start = CMbDT::time();
    }
    if ($this->_transfusion_end =="current") {
      $this->_transfusion_end = CMbDT::time();
    }
    
    if ($this->_recuperation_start !== null && $this->_recuperation_start != "") {
      $this->_recuperation_start = CMbDT::time($this->_recuperation_start);
      $this->recuperation_start = CMbDT::addDateTime($this->_recuperation_start, CMbDT::date($this->_datetime));
    }
    if ($this->_recuperation_start === "") {
        $this->recuperation_start= "";
    }
    if ($this->_recuperation_end !== null && $this->_recuperation_end != "") {
      $this->_recuperation_end = CMbDT::time($this->_recuperation_end);
      $this->recuperation_end = CMbDT::addDateTime($this->_recuperation_end, CMbDT::date($this->_datetime));
    }
    if ($this->_recuperation_end === "") {
      $this->recuperation_end= "";
    }
    if ($this->_transfusion_start !== null && $this->_transfusion_start != "") {
      $this->_transfusion_start = CMbDT::time($this->_transfusion_start);
      $this->transfusion_start = CMbDT::addDateTime($this->_transfusion_start, CMbDT::date($this->_datetime));
    }
    if ($this->_transfusion_start === "") {
      $this->transfusion_start= "";
    }
    if ($this->_transfusion_end !== null && $this->_transfusion_end != "") {
      $this->_transfusion_end = CMbDT::time($this->_transfusion_end);
      $this->transfusion_end = CMbDT::addDateTime($this->_transfusion_end, CMbDT::date($this->_datetime));
    }
    if ($this->_transfusion_end === "") {
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
    
    $this->notify("BeforeFillLimitedTemplate", $template);
    
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
       
    if ($this->_ref_incident_type->_view) {
      $template->addProperty("Cell Saver - Incident transfusionnel", $this->_ref_incident_type->_view);    
    }
    else {
      $template->addProperty("Cell Saver - Incident transfusionnel", "Aucun incident signalé");    
    }
    
    $this->notify("AfterFillLimitedTemplate", $template);
  }
}
