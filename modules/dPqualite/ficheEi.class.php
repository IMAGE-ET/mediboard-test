<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
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
  var $vraissemblance             = null;
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
  var $suite_even_descr           = null;
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
  var $_ref_evenement       = null;
  var $_ref_items           = null;
  var $_etat_actuel         = null;
  var $_criticite           = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'fiches_ei';
    $spec->key   = 'fiche_ei_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["user_id"]                    = "notNull ref class|CMediusers";    
    $specs["date_fiche"]                 = "notNull dateTime";
    $specs["date_incident"]              = "notNull dateTime";
    $specs["evenements"]                 = "notNull str maxLength|255";
    $specs["lieu"]                       = "notNull str maxLength|50";
    $specs["type_incident"]              = "notNull enum list|inc|ris";
    $specs["elem_concerne"]              = "notNull enum list|pat|vis|pers|med|mat";
    $specs["elem_concerne_detail"]       = "notNull text";
    $specs["autre"]                      = "text";
    $specs["descr_faits"]                = "notNull text";
    $specs["mesures"]                    = "notNull text";
    $specs["descr_consequences"]         = "notNull text";
    $specs["suite_even"]                 = "notNull enum list|trans|plong|deces|autre";
    $specs["suite_even_descr"]           = "text";
    $specs["deja_survenu"]               = "enum list|non|oui";
    
    //Prise en charge de la fiche
    $specs["degre_urgence"]              = "enum list|1|2|3|4";
    $specs["gravite"]                    = "enum list|1|2|3|4|5";
    $specs["vraissemblance"]             = "enum list|1|2|3|4|5";
    $specs["plainte"]                    = "enum list|non|oui";
    $specs["commission"]                 = "enum list|non|oui";
    $specs["annulee"]                    = "bool";
    $specs["remarques"]                  = "text";
    
    //1ere Validation Qualité
    $specs["valid_user_id"]              = "ref class|CMediusers";
    $specs["date_validation"]            = "dateTime";
    
    //Validation Chef de Projet
    $specs["service_valid_user_id"]      = "ref class|CMediusers";
    $specs["service_date_validation"]    = "dateTime";
    $specs["service_actions"]            = "text";
    $specs["service_descr_consequences"] = "text";
    
    //2nde Validation Qualité
    $specs["qualite_user_id"]            = "ref class|CMediusers";
    $specs["qualite_date_validation"]    = "dateTime";
    $specs["qualite_date_verification"]  = "date";
    $specs["qualite_date_controle"]      = "date";
    
    // Form fields
    $specs["_incident_heure"]            = "notNull time";
    $specs["_incident_date"]             = "notNull date";
    return $specs;
  }
  
  function loadRefsAuthor(){
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
    $this->_ref_user->loadRefFunction();
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->loadRefsAuthor();
    
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
  
  function loadCriticite() {
    // Calcul de la criticité
    if($this->gravite && $this->vraissemblance) {
      $tabCriticite = array(
        1 => array(1 => 1, 2 => 1, 3 => 1, 4 => 2, 5 => 2),
        2 => array(1 => 1, 2 => 2, 3 => 2, 4 => 2, 5 => 3),
        3 => array(1 => 1, 2 => 2, 3 => 2, 4 => 3, 5 => 3),
        4 => array(1 => 2, 2 => 2, 3 => 3, 4 => 3, 5 => 3),
        5 => array(1 => 3, 2 => 3, 3 => 3, 4 => 3, 5 => 3),
      );
      $this->_criticite = $tabCriticite[$this->gravite][$this->vraissemblance];
    }
  }
    
  function updateFormFields() {
    parent::updateFormFields();
    
    if($this->date_incident){
      $this->_incident_heure = substr($this->date_incident, 11, 5);
      $this->_incident_date  = substr($this->date_incident, 0, 10);
    }
    
    if($this->evenements){
      $this->_ref_evenement = explode('|', $this->evenements);
    }
    
    if($this->qualite_date_controle) {
      $this->_etat_actuel = CAppUI::tr("_CFicheEi_acc-CTRL_OK");
    } elseif(!$this->service_date_validation && $this->service_valid_user_id){
      $this->_etat_actuel = CAppUI::tr("_CFicheEi_acc-ATT_CS_adm");
    } elseif(!$this->qualite_user_id){
    	$this->_etat_actuel = CAppUI::tr("_CFicheEi_acc-ATT_QUALITE_adm");
    } elseif(!$this->qualite_date_validation){
      $this->_etat_actuel = CAppUI::tr("_CFicheEi_acc-ATT_QUALITE_adm");
    } elseif(!$this->qualite_date_verification){
      $this->_etat_actuel = CAppUI::tr("_CFicheEi_acc-ATT_VERIF");
    } else {
      $this->_etat_actuel = CAppUI::tr("_CFicheEi_acc-ATT_CTRL");
    }
    
    $this->loadCriticite();
    
    $this->_view = sprintf("%03d - %s", $this->fiche_ei_id, mbDateToLocale(substr($this->date_fiche, 0, 10)));
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
    if($this->_incident_date !== null && $this->_incident_heure !== null){
      $this->date_incident = "$this->_incident_date $this->_incident_heure:00";
    }
  }
  
  function canDeleteEx() {
    return CAppUI::tr("CFicheEi-msg-canDelete");
  }
  
  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array(), $countOnly = false) {
		$ljoin["users_mediboard"] = "users_mediboard.user_id = fiches_ei.user_id";
		$ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";
    // Filtre sur l'établissement
		$g = CGroups::loadCurrent();
		$where["functions_mediboard.group_id"] = "= '$g->_id'";
    
    return $countOnly ? 
              $this->countList($where, null, $limit, $groupby, $ljoin) :
              $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }
  
  function loadFichesEtat($etat, $user_id = null, $where_termine = null, $annule = 0, $countOnly = false, $first = 0){
    $where = array();
    $where["annulee"] = "= '$annule'";
    
    switch ($etat) {
      case "AUTHOR":
        $where["fiches_ei.user_id"] = "= '$user_id'";
        break;
      case "VALID_FICHE":
        $where["fiches_ei.date_validation"] = " IS NULL";
        break;
      case "ATT_CS":
        $where["fiches_ei.date_validation"]         = " IS NOT NULL";
        $where["fiches_ei.service_date_validation"] = " IS NULL";
        if($user_id){
          $where["fiches_ei.service_valid_user_id"] = "= '$user_id'";
        }
        break;
      case "ATT_QUALITE":
        $where["fiches_ei.service_date_validation"] = " IS NOT NULL";
        $where["fiches_ei.qualite_date_validation"] = " IS NULL";
        if($user_id){
          $where["fiches_ei.service_valid_user_id"] = "= '$user_id'";
        }
        break;
      case "ATT_VERIF":
        $where["fiches_ei.qualite_date_validation"]   = " IS NOT NULL";
        $where["fiches_ei.qualite_date_verification"] = " IS NULL";
        $where["fiches_ei.qualite_date_controle"]     = " IS NULL";
        break;
      case "ATT_CTRL":
        $where["fiches_ei.qualite_date_verification"] = " IS NOT NULL";
        $where["fiches_ei.qualite_date_controle"]     = " IS NULL";
        break;
      case "ALL_TERM":
        if($user_id){
          $where["fiches_ei.service_valid_user_id"]   = "= '$user_id'";
          $where["fiches_ei.qualite_date_validation"] = " IS NOT NULL";
        }else{
          if($where_termine){
            $where = array_merge($where, $where_termine);
          }
          $where["fiches_ei.qualite_date_controle"]   = " IS NOT NULL";
        }
        break;
      case "ANNULE":
        $where["annulee"] = "= '1'";
        break;
    }
    $order = "fiches_ei.date_incident DESC, fiches_ei.fiche_ei_id DESC";
    $listFiches = new CFicheEi();
    if ($countOnly) {
      return $listFiches->loadGroupList($where, null, null, null, null, true);
    }
    else {
      $listFiches = $listFiches->loadGroupList($where, $order, ($first+0).',20');
      foreach($listFiches as &$fiche){
        $fiche->loadRefsFwd();
      }
      return $listFiches;
    }
  }
}
?>