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
  var $user_id                    = null;
  var $valid_user_id              = null;
  var $date_fiche                 = null;
  var $date_incident              = null;
  var $date_validation            = null;
  var $evenements                 = null;
  var $lieu                       = null;
  var $type_incident              = null;
  var $elem_concerne              = null;
  var $elem_concerne_detail       = null;
  var $autre                      = null;
  var $descr_faits                = null;
  var $mesures                    = null;
  var $descr_consequences         = null;
  var $gravite                    = null;
  var $plainte                    = null;
  var $commission                 = null;
  var $deja_survenu               = null;
  var $degre_urgence              = null;
  var $service_valid_user_id      = null;
  var $service_date_validation    = null;
  var $service_actions            = null;
  var $service_descr_consequences = null;
  var $qualite_user_id            = null;
  var $qualite_date_validation    = null;
  var $qualite_date_verification  = null;
  var $qualite_date_controle      = null;
  var $suite_even                 = null;
  var $annulee                    = null;
  var $remarques                  = null;
  
  // Object References
  var $_ref_user            = null;
  var $_ref_user_valid      = null;
  var $_ref_service_valid   = null;
  var $_ref_qualite_valid   = null;

  // Form fields
  var $_incident_date       = null;
  var $_incident_heure      = null;
  var $_incident_min        = null;
  var $_ref_evenement       = null;
  var $_ref_items           = null;
  var $_etat_actuel         = null;
  
  function CFicheEi() {
    $this->CMbObject("fiches_ei", "fiche_ei_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "user_id"                      => "ref|notNull",    
      "date_fiche"                   => "dateTime|notNull",
      "date_incident"                => "dateTime|notNull",
      "evenements"                   => "str|maxLength|255|notNull",
      "lieu"                         => "str|maxLength|50|notNull",
      "type_incident"                => "enum|inc|ris|notNull",
      "elem_concerne"                => "enum|pat|vis|pers|med|mat|notNull",
      "elem_concerne_detail"         => "text|notNull",
      "autre"                        => "text",
      "descr_faits"                  => "text|notNull",
      "mesures"                      => "text|notNull",
      "descr_consequences"           => "text|notNull",
      "gravite"                      => "enum|nul|mod|imp|notNull",
      "suite_even"                   => "enum|trans|plong|deces|autre|notNull",
      "plainte"                      => "enum|non|oui|notNull",
      "commission"                   => "enum|non|oui|notNull",
      "deja_survenu"                 => "enum|non|oui",
      "degre_urgence"                => "enum|1|2|3|4",
      "annulee"                      => "enum|0|1",
      "remarques"                    => "text",
      //1ere Validation Qualité
      "valid_user_id"                => "ref",
      "date_validation"              => "dateTime",
      //Validation Chef de Projet
      "service_valid_user_id"        => "ref",
      "service_date_validation"      => "dateTime",
      "service_actions"              => "text",
      "service_descr_consequences"   => "text",
      //2nde Validation Qualité
      "qualite_user_id"              => "ref",
      "qualite_date_validation"      => "dateTime",
      "qualite_date_verification"    => "date",
      "qualite_date_controle"        => "date"
    );
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
    $this->_ref_service_valid = new CMediusers;
    if($this->service_valid_user_id){
      $this->_ref_service_valid->load($this->service_valid_user_id);
    }
    $this->_ref_qualite_valid = new CMediusers;
    if($this->qualite_user_id){
      $this->_ref_qualite_valid->load($this->qualite_user_id);
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
    
    if(!$this->service_date_validation && $this->service_valid_user_id){
      $this->_etat_actuel = "Att. de Validation du Chef de Service";
    }elseif(!$this->qualite_user_id){
    	$this->_etat_actuel = "Att. de Validation Qualité";
    }elseif(!$this->qualite_date_verification){
      $this->_etat_actuel = "Att. de Vérification";
    }elseif(!$this->qualite_date_controle){
      $this->_etat_actuel = "Att. de Contrôle";
    }
    $this->_view = str_pad($this->fiche_ei_id, 3, "0", STR_PAD_LEFT). " - ".substr($this->date_fiche,8,2)."/".substr($this->date_fiche,5,2)."/".substr($this->date_fiche,0,4);
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