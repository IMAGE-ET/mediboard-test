<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @abstract Permet d'ajouter des transmissions médicales à un séjour 
 */

class CTransmissionMedicale extends CMbMetaObject {
  // DB Table key
  var $transmission_medicale_id = null;	
  
  // DB Fields
  var $sejour_id   = null;
  var $user_id     = null;
  var $degre       = null;
  var $date        = null;
  var $text        = null;
  var $type        = null;
  var $libelle_ATC = null;
  
  // References
  var $_ref_sejour = null;
  var $_ref_user   = null;
  var $_ref_cible  = null;
  
  var $_cible = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'transmission_medicale';
    $spec->key   = 'transmission_medicale_id';
    return $spec;
  }

  function getProps() {
  	$props = parent::getProps();
    $props["object_id"]    = "ref class|CMbObject meta|object_class cascade";
  	$props["object_class"] = "enum list|CPrescriptionLineElement|CPrescriptionLineMedicament|CPrescriptionLineComment|CCategoryPrescription|CAdministration|CPerfusion";
    $props["sejour_id"]    = "ref notNull class|CSejour";
    $props["user_id"]      = "ref notNull class|CMediusers";
    $props["degre"]        = "enum notNull list|low|high default|low";
    $props["date"]         = "dateTime notNull";
    $props["text"]         = "text helped";
    $props["type"]         = "enum list|data|action|result";
    $props["libelle_ATC"]  = "text";
    return $props;
  }
  
  function loadRefSejour(){
  	$this->_ref_sejour = new CSejour;
    $this->_ref_sejour = $this->_ref_sejour->getCached($this->sejour_id);
  }
  
  function loadRefUser(){
    $this->_ref_user = new CMediusers;
    $this->_ref_user = $this->_ref_user->getCached($this->user_id);
		$this->_ref_user->loadRefFunction();
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->loadRefSejour();
    $this->loadRefUser();
  	$this->_view = "Transmission de ".$this->_ref_user->_view;
  }
  
  function calculCibles(&$cibles = array()){
    if($this->object_id && $this->object_class){
      // Ligne de medicament, cible => classe ATC
      if($this->object_class == "CPrescriptionLineMedicament"){
        $this->_ref_object->_ref_produit->loadClasseATC();
        $libelle_ATC = $this->_ref_object->_ref_produit->_ref_ATC_2_libelle;
        $this->_cible = $libelle_ATC;
        if(!isset($cibles["ATC"]) || !in_array($libelle_ATC, $cibles["ATC"])){
          $cibles["ATC"][] = $libelle_ATC;
        }
      }
      
      // Ligne d'element, cible => categorie
      if($this->object_class == "CPrescriptionLineElement"){
        $category = $this->_ref_object->_ref_element_prescription->_ref_category_prescription;
        $this->_cible = $category->_view;
        $cibles["CCategoryPrescription"][$category->_id] = $category->_view;
      }
      
      // Administration => ATC ou categorie
      if($this->object_class == "CAdministration"){
        if($this->_ref_object->object_class == "CPrescriptionLineMedicament"){
          $this->_ref_object->loadTargetObject();
          $this->_ref_object->_ref_object->_ref_produit->loadClasseATC();
	        $libelle_ATC = $this->_ref_object->_ref_object->_ref_produit->_ref_ATC_2_libelle;
	        $this->_cible = $libelle_ATC;
	        if(!isset($cibles["ATC"]) || !in_array($libelle_ATC, $cibles["ATC"])){
	          $cibles["ATC"][] = $libelle_ATC;
	        }
        }
        if($this->_ref_object->object_class == "CPrescriptionLineElement"){
          $this->_ref_object->loadTargetObject();
          $category = $this->_ref_object->_ref_object->_ref_element_prescription->_ref_category_prescription;
          $this->_cible = $category->_view;
          $cibles["CCategoryPrescription"][$category->_id] = $category->_view;
        }
      }
      
      if($this->object_class == "CCategoryPrescription"){
        $this->_cible = $this->_ref_object->_view;
        $cibles[$this->object_class][$this->object_id] = $this->_ref_object->_view;
      }
      
      if($this->object_class == "CPerfusion"){
        $this->_cible = "perfusion";
        $cibles["perf"][0] = "perfusion";
      }
    }
    
    if($this->libelle_ATC){
      $this->_cible = $this->libelle_ATC;
      if(!isset($cibles["ATC"]) || !in_array($this->libelle_ATC, $cibles["ATC"])){
        $cibles["ATC"][] = $this->libelle_ATC;
      }
    }
  }
  
  
  function getPerm($perm) {
    if(!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }
}

?>