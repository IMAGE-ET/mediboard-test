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

  // Form fields
  var $_modificateurs = array();
  var $_anesth = null;
  
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
    $this->_view = "$this->code_acte-$this->code_activite-$this->code_phase-$this->modificateurs"; 
  
    $this->_anesth=($this->code_activite==4)?true:false;
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
    //$this->loadRefOperation();
    parent::loadRefsFwd();
    //$this->loadRefObject();
    $this->loadRefExecutant();
    $this->loadRefCodeCCAM();
  }
  
  function getFavoris($chir,$class,$view){
  	//$vue=($view=="taux")?$vue="nb_acte DESC":$vue="code_acte ASC";
  	$condition=($class=="")?"executant_id = '$chir'":
  	"executant_id = '$chir' AND object_class = '$class'";
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
}

?>