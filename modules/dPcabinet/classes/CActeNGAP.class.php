<?php
  
/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

class CActeNGAP extends CActe {
  // DB Table key
  var $acte_ngap_id = null;
  
  // DB fields
  var $quantite    = null;
  var $code        = null;
  var $coefficient = null;
  var $demi        = null;
  var $complement  = null;
  var $lettre_cle  = null;
  
  // Distant fields
  var $_libelle    = null;
  var $_execution = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = 'acte_ngap';
    $spec->key    = 'acte_ngap_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["code"]                = "str notNull maxLength|3";
    $specs["quantite"]            = "num notNull maxLength|2";
    $specs["coefficient"]         = "float notNull";
    $specs["demi"]                = "enum list|0|1 default|0";
    $specs["complement"]          = "enum list|N|F|U";
    
    $specs["lettre_cle"]          = "enum list|0|1 notNull default|0";
   
    $specs["_execution"]          = "dateTime";
    
    
    return $specs;
  }
 
  function updateFormFields() {
    parent::updateFormFields();
    
    // Vue cod�e
    $this->_shortview = $this->quantite > 1 ? "{$this->quantite}x" : "";
    $this->_shortview.= $this->code;
    if ($this->coefficient != 1) {
      $this->_shortview.= $this->coefficient;      
    }
    if ($this->demi) {
      $this->_shortview.= "/2";
    }
    
    $this->_view = "Acte NGAP $this->_shortview";
    if ($this->object_class && $this->object_id) {
      $this->_view .= " de $this->object_class-$this->object_id";
    }
  }
  
  function updatePlainFields() {
    parent::updatePlainFields();
    
    if ($this->code) {
      $this->code = strtoupper($this->code);
    }
  }
  
  function loadExecution() {
    $this->loadTargetObject();
    $this->_ref_object->getActeExecution();
    $this->_execution = $this->_ref_object->_acte_execution;
  }
  
  /**
   * CActe redefinition
   * @return string Serialised full code
   */
  function makeFullCode() {
      return $this->quantite.
        "-". $this->code.
        "-". $this->coefficient.
        "-". $this->montant_base.
        "-". str_replace("-","*", $this->montant_depassement).
        "-". $this->demi.
        "-". $this->complement; 
  }

  /**
   * CActe redefinition
   * @param string $code Serialised full code
   * @return void
   */
  function setFullCode($code){
    $details = explode("-", $code);

    $this->quantite    = $details[0];
    $this->code        = $details[1];
    $this->coefficient = $details[2];

    if (count($details) >= 4) {
      $this->montant_base = $details[3];
    }

    if (count($details) >= 5){
      $this->montant_depassement = str_replace("*","-",$details[4]);
    }
    
    if (count($details) >= 6){
    	$this->demi = $details[5];
    }

    if (count($details) >= 7){
    	$this->complement = $details[6];
    }
    $this->getLibelle();
    if(!$this->lettre_cle){
    	$this->lettre_cle = 0;
    }
    
    $this->updateFormFields();
  }
  
  function getPrecodeReady() {
    return $this->quantite && $this->code && $this->coefficient;
  }
  
  function check(){
    if ($msg = $this->checkCoded()){
      return $msg;
    }
    
    return parent::check();
  }
 
  function canDeleteEx() {
    if ($msg = $this->checkCoded()){
      return $msg;
    }
    
    return parent::canDeleteEx();
  }
  
	function updateMontantBase() {
		$ds = CSQLDataSource::get("ccamV2");
    $query = "SELECT `tarif` 
		  FROM `codes_ngap` 
			WHERE `code` = %";
		$query = $ds->prepare($query, $this->code);
		
		$this->montant_base = $ds->loadResult($query);
		$this->montant_base *= $this->coefficient;
	  $this->montant_base *= $this->quantite;

		if ($this->demi) {
		  $this->montant_base /= 2;
		}
		
	  if ($this->complement == "F") {
	    $this->montant_base += 19.06;  
	  }
		
	  if ($this->complement == "N") {
	    $this->montant_base += 25; 
	  }
		
		return $this->montant_base;
	}
	
  function getLibelle() {
    $ds = CSQLDataSource::get("ccamV2");
    $query = "SELECT `libelle`, `lettre_cle`
		  FROM codes_ngap 
			WHERE CODE = % ";
    $query = $ds->prepare($query, $this->code);

    $this->_libelle = "Acte inconnu ou supprim�";
     
    $hash = $ds->loadHash($query);
    if ($hash) {
     $this->_libelle   = $hash['libelle'];
     /* on r�cup�re au passage la lettre cle pour l'utiliser 
      * dans le remplissage automatique de ce champ dans la cotation 
      */
     $this->lettre_cle = $hash['lettre_cle']; 
    }
		return $this->_libelle;
  }
} 

?>