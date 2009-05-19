<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CAffectationPersonnel extends CMbMetaObject {
  // DB Table key
  var $affect_id = null;
  
  // DB references
  var $personnel_id = null;
  
  // DB fields
  var $realise = null;
  var $debut   = null;
  var $fin     = null;

  // Form fields
  var $_debut  = null;
  var $_fin    = null;
  
  // References
  var $_ref_personnel = null;
  var $_ref_object = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'affectation_personnel';
    $spec->key   = 'affect_id';
    return $spec;
  }
	
  function getProps() {
    $specs = parent::getProps();
    $specs["personnel_id"] = "ref notNull class|CPersonnel";
    $specs["realise"]  = "bool notNull";
    $specs["debut"]    = "dateTime";
    $specs["fin"]      = "dateTime moreThan|debut";
    
    $specs["_debut"]   = "time";
    $specs["_fin"]     = "time moreThan|_debut";
    
    return $specs;
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefPersonnel();
  }
  
  function loadRefPersonnel(){
  	$this->_ref_personnel = new CPersonnel();
  	$this->_ref_personnel = $this->_ref_personnel->getCached($this->personnel_id);
  }
  
  function loadRefObject(){
    $this->_ref_object = new $this->object_class;
    $this->_ref_object = $this->_ref_object->getCached($this->object_id);
  }
 
  
  function check(){
    if ($msg = parent::check()) {
      return $msg;
    }  
  }
  
  /**
   * Trouve les affectations avec cible et personnel identique
   * @return $array Liste des siblings
   */
  function getSiblings() {
    // Version complete
    $clone = new CAffectationPersonnel();
    $clone->load($this->_id);
    $clone->extendsWith($this);
    
    // Filtre exact
    $sibling = new CAffectationPersonnel();
    $sibling->object_class = $clone->object_class;
    $sibling->object_id    = $clone->object_id;
    $sibling->personnel_id = $clone->personnel_id;
    
    // Chargement des siblings
    $siblings = $sibling->loadMatchingList();
    unset($siblings[$this->_id]);
    return $siblings;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefs();  
    if ($this->object_class == "CPlageOp"){
      $this->_debut = mbAddDateTime($this->_ref_object->debut, $this->_ref_object->date);
    	$this->_fin = mbAddDateTime($this->_ref_object->fin, $this->_ref_object->date);
    }
    
    if ($this->object_class == "COperation" || $this->object_class == "CBloodSalvage" ){
      if($this->debut){
        $this->_debut = mbTime($this->debut);
      }
      if($this->fin){
        $this->_fin   = mbTime($this->fin);
      }
    }
  }
  
  function updateDBFields(){
    if($this->object_class == "COperation" || $this->object_class == "CBloodSalvage" ){
      $this->loadRefObject();
      $this->_ref_object->loadRefPlageOp();
      
      if($this->_debut =="current") {
      	$this->_debut = mbTime();
      }
      
      if($this->_fin =="current") {
      	$this->_fin = mbTime();
      }

      if($this->_debut !== null && $this->_debut != ""){
        $this->_debut = mbTime($this->_debut);
        $this->debut = mbAddDateTime($this->_debut, mbDate($this->_ref_object->_datetime));  
      }
      
      if($this->_fin !== null && $this->_fin != ""){
        $this->_fin = mbTime($this->_fin);
        $this->fin = mbAddDateTime($this->_fin, mbDate($this->_ref_object->_datetime));  
      }
      
      // Suppression de la valeur
      if($this->_debut === ""){
        $this->debut = "";
      }
      if($this->_fin === ""){
        $this->fin = "";
      } 
      
      // Mise a jour du champ realise
      if($this->debut !== null && $this->fin !== null){
        $this->realise = 1;
      }
      
      if($this->debut === "" || $this->fin === ""){
        $this->realise = 0;
      }
    }
  }
}
?>