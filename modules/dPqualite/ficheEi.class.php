<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */


/**
 * The CFicheEi class
 */
class CFicheEi extends CMbObject {
  // DB Table key
  var $fiche_ei_id = null;
    
  // DB Fields
  var $user_id              = null;
  var $valid_user_id        = null;
  var $date_fiche           = null;
  var $date_incident        = null;
  var $date_validation      = null;
  var $evenements           = null;
  var $lieu                 = null;
  var $type_incident        = null;
  var $elem_concerne        = null;
  var $elem_concerne_detail = null;
  var $autre                = null;
  var $descr_faits          = null;
  var $mesures              = null;
  var $descr_consequences   = null;
  var $gravite              = null;
  var $plainte              = null;
  var $commission           = null;
  var $deja_survenu         = null;
  var $degre_urgence        = null;

  // Object References
  var $_ref_user            = null;
  var $_ref_user_valid      = null;

  // Form fields
  var $_incident_date       = null;
  var $_incident_heure      = null;
  var $_incident_min        = null;
  var $_ref_evenement       = null;
  var $_ref_items           = null;
  
  function CFicheEi() {
    $this->CMbObject("fiches_ei", "fiche_ei_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    
    $this->_props["user_id"]              = "ref|notNull";
    $this->_props["valid_user_id"]        = "ref";
    $this->_props["date_fiche"]           = "dateTime|notNull";
    $this->_props["date_incident"]        = "dateTime|notNull";
    $this->_props["date_validation"]      = "dateTime";
    $this->_props["evenements"]           = "str|maxLength|255|notNull";
    $this->_props["lieu"]                 = "str|maxLength|50|notNull";
    $this->_props["type_incident"]        = "enum|0|1|notNull";
    $this->_props["elem_concerne"]        = "enum|0|1|2|3|4|notNull";
    $this->_props["elem_concerne_detail"] = "text|notNull";
    $this->_props["autre"]                = "text";
    $this->_props["descr_faits"]          = "text";
    $this->_props["mesures"]              = "text";
    $this->_props["descr_consequences"]   = "text";
    $this->_props["gravite"]              = "enum|0|1|2|notNull";
    $this->_props["plainte"]              = "enum|0|1|notNull";
    $this->_props["commission"]           = "enum|0|1|notNull";
    $this->_props["deja_survenu"]         = "enum|0|1";
    $this->_props["degre_urgence"]        = "enum|1|2|3|4";
    
    $this->buildEnums();
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_ref_user->loadRefFunction();
    
    $this->_ref_user_valid = new CMediusers;
    if($this->valid_user_id){
      $this->_ref_user_valid->load($this->valid_user_id);
    }
  }
    
  function updateFormFields() {
    parent::updateFormFields();
    if($this->date_incident){
      $this->_incident_heure = substr($this->date_incident, 11, 2);
      $this->_incident_min   = substr($this->date_incident, 14, 2);
      $this->_incident_date  = substr($this->date_incident, 0, 4)."-".substr($this->date_incident, 5, 2)."-".substr($this->date_incident, 8, 2);
    }
    if($this->evenements){
      $this->_ref_evenement = explode("|", $this->evenements);
    }
  }
  
  function loadRefItems() {
    $this->_ref_items = array();
    foreach ($this->_ref_evenement as $evenement) {
      $ext_item = new CEiItem();
      $ext_item->load($evenement);
      $this->_ref_items[] = $ext_item;
    }
  }
  
  function updateDBFields() {
    if($this->_incident_date!==null && $this->_incident_heure!==null && $this->_incident_min!=null){
      $this->date_incident = $this->_incident_date." ";
      $this->date_incident .= $this->_incident_heure .":";
      $this->date_incident .= $this->_incident_min .":00";
    }
  }
  
  function canDelete(&$msg, $oid = null) {
    $msg = "Il n'est pas possible de supprimer une fiche d'EI.";
    return false;
  }
}
?>