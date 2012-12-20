<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision$
* @author Romain Ollivier
*/

class CPlageressource extends CPlageHoraire {
  const OUT     = "#aaa";  // plage échue
  const FREE    = "#aae";  // plage libre
  const FREEB   = "#88c";  // plage libre à plus d'1 mois
  const BUSY    = "#ecc";  // plage occupée
  const BLOCKED = "#eaa";  // plage occupée à moins de 15 jours
  const PAYED   = "#aea";  // plage réglée
  
  // DB Table key
  var $plageressource_id = null;

  // DB References
  var $prat_id = null;

  // DB fields
  var $tarif   = null;
  var $libelle = null;
  var $paye    = null;

  // Form fields
  var $_hour_deb = null;
  var $_min_deb  = null;
  var $_hour_fin = null;
  var $_min_fin  = null;
  var $_state    = null;

  //Filter Fields
  var $_date_min = null;
  var $_date_max = null;

  // Object References
  var $_ref_prat     = null;
  var $_ref_patients = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table          = "plageressource";
    $spec->key            = "plageressource_id";
    $spec->collision_keys = array();
    return $spec;
  }
  
  function getProps() {
  	$props = parent::getProps();
    $props["prat_id"] = "ref class|CMediusers seekable";
    $props["tarif"] = "currency notNull min|0";
    $props["libelle"] = "str confidential seekable";
    $props["paye"] = "bool";
    $props["_date_min"] = "date";
    $props["_date_max"] = "date moreEquals|_date_min";
    $props["_hour_deb"] = "time";
    $props["_hour_fin"] = "time";
    return $props;
  }
  
  function loadRefsFwd() {
    $this->_ref_prat = new CMediusers();
    $this->_ref_prat->load($this->prat_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_prat) {
      $this->loadRefsFwd();
    }
    return $this->_ref_prat->getPerm($permType);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_hour_deb = mbTransformTime($this->debut, null, "%H");
    $this->_hour_fin = mbTransformTime($this->fin  , null, "%H");
		
		// State rules
    if ($this->paye == 1) {
      $this->_state = self::PAYED;
    } 
    elseif($this->date < mbDate()) {
      $this->_state = self::OUT;
    } 
    elseif($this->prat_id) {
      if (mbDate("+ 15 DAYS") > $this->date) {
        $this->_state = self::BLOCKED;
      } 
      else {
        $this->_state = self::BUSY;
      }
    } 
    elseif (mbDate("+ 1 MONTH") < $this->date) {
      $this->_state = self::FREEB;
    } 
    else {
      $this->_state = self::FREE;
    }
  }
  
  function becomeNext() {
    // Store old datas
    $prat_id = $this->prat_id;
    $libelle = $this->libelle;
    $tarif   = $this->tarif;
    
    // Store old form fields
    $_hour_deb = $this->_hour_deb;
    $_min_deb  = $this->_min_deb;
    $_hour_fin = $this->_hour_fin;
    $_min_fin  = $this->_min_fin;

    $this->date = mbDate("+7 DAYS", $this->date);
    $where["date"] = "= '$this->date'";
    $where[] = "`debut` = '$this->debut' OR `fin` = '$this->fin'";
    if (!$this->loadObject($where)) {
      $this->plageressource_id = null;
    }

    // Restore old fields
    $this->prat_id   = $prat_id;
    $this->libelle   = $libelle;
    $this->tarif     = $tarif;
    $this->_hour_deb = $_hour_deb;
    $this->_min_deb  = $_min_deb;
    $this->_hour_fin = $_hour_fin;
    $this->_min_fin  = $_min_fin;
    $this->updatePlainFields();
  }    
}

?>