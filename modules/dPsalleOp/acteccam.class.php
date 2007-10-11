<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage mediusers
 *	@version $Revision$
 *  @author Thomas Despoix
 */

/**
 * Classe servant à gérer les enregistrements des actes CCAM pendant les
 * interventions
 */
class CActeCCAM extends CMbMetaObject {
  // DB Table key
	var $acte_id = null;

  // DB References
  var $executant_id        = null;

  // DB Fields
  var $code_acte           = null;
  var $code_activite       = null;
  var $code_phase          = null;
  var $execution           = null;
  var $modificateurs       = null;
  var $montant_depassement = null;
  var $commentaire         = null;
  var $code_association    = null;

  // Form fields
  var $_modificateurs     = array();
  var $_anesth            = null;
  var $_linked_actes      = null;
  var $_guess_association = null;
  
  // Object references
  var $_ref_executant = null;
  var $_ref_code_ccam = null;

	function CActeCCAM() {
		$this->CMbObject( "acte_ccam", "acte_id" );
    
    $this->loadRefModule(basename(dirname(__FILE__)));
	}
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs["object_class"]        = "notNull enum list|COperation|CSejour|CConsultation";
    $specs["code_acte"]           = "notNull code ccam";
    $specs["code_activite"]       = "notNull num minMax|0|99";
    $specs["code_phase"]          = "notNull num minMax|0|99";
    $specs["execution"]           = "notNull dateTime";
    $specs["modificateurs"]       = "str maxLength|4";
    $specs["montant_depassement"] = "currency min|0";
    $specs["commentaire"]         = "text";
    $specs["executant_id"]        = "notNull ref class|CMediusers";
    $specs["code_association"]    = "num minMax|1|5";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "code_acte" => "equal"
    );
  }
  
  function check() {
    return parent::check(); 

    // datetime_execution: attention à rester dans la plage de l'opération
  }
   
  function updateFormFields() {
    parent::updateFormFields();
    $this->_modificateurs = str_split($this->modificateurs);
    $this->_view   = "$this->code_acte-$this->code_activite-$this->code_phase-$this->modificateurs";
    $this->_anesth = ($this->code_activite == 4) ? true : false;
  }
  
  function loadRefObject(){
    $this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id); 
  }
 
  function loadRefExecutant() {
    $this->_ref_executant = new CMediusers;
    $this->_ref_executant->load($this->executant_id);
  }
  
  function loadRefCodeCCAM() {
    $this->_ref_code_ccam = new CCodeCCAM($this->code_acte);
    $this->_ref_code_ccam->load();
  }
   
  function loadRefsFwd() {
    parent::loadRefsFwd();

    $this->loadRefExecutant();
    $this->loadRefCodeCCAM();
  }
  
  function getFavoris($chir,$class,$view) {
  	$condition = ( $class == "" ) ? "executant_id = '$chir'" : "executant_id = '$chir' AND object_class = '$class'";
  	$sql = "select code_acte, object_class, count(code_acte) as nb_acte
            from acte_ccam
            where $condition
            group by code_acte
            order by nb_acte DESC
            limit 10";
  	$codes = $this->_spec->ds->loadlist($sql);
  	return $codes;
  }
  
  function getPerm($permType) {
    if(!$this->_ref_object) {
    	$this->loadRefObject();
    }
    return $this->_ref_object->getPerm($permType);
  }
  
  function getLinkedActes() {
    $acte = new CActeCCAM();
    
    $where = array();
    $where["acte_id"]       = "<> '$this->_id'";
    $where["object_class"]  = "= '$this->object_class'";
    $where["object_id"]     = "= '$this->object_id'";
    $where["code_activite"] = "= '$this->code_activite'";
    
    $this->_linked_actes = $acte->loadList($were);
  }
  
  function guessAssociation() {
    $this->getLinkedActes();
    $this->loadRefCodeCCAM();
    
    // Cas d'un seul actes
    if(!count($this->_linked_actes)) {
      $this->_guess_association = null;
      return $this->_guess_association;
    }
    
    // Cas général pour plusieurs actes
    $tarif = $this->_ref_code_ccam->activites[$this->code_activite]->phases[$this->code_phase]->tarif;
    $orderedActes = array();
    $orderedActes[$this->_id] = $tarif;
    foreach($this->_linked_actes as &$acte) {
      $acte->loadRefCodeCCAM();
      $tarif = $acte->_ref_code_ccam->activites[$acte->code_activite]->phases[$acte->code_phase]->tarif;
      $orderedActes[$acte->_id] = $tarif;
    }
    asort($orderedActes);
    $position = array_search($this->_id, array_keys($orderedActes));
    
    switch($position) {
      case 0 :
        $this->_guess_association = 1;
        break;
      case 1 :
        $this->_gess_association = 2;
        break;
      default :
        $this->_guess_association = "X";
    }
    
    return $this->_guess_association;
  }
}

?>