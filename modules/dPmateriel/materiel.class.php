<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPmateriel
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CMateriel class
 */
class CMateriel extends CMbObject {
  // DB Table key
  var $materiel_id = null;
  
  // DB Fields
  var $nom        = null;
  var $code_barre = null;
  var $description = null;
  var $category_id = null;

  // Object References
  var $_ref_stock       = null;
  var $_ref_refMateriel = null;
  var $_ref_category    = null;
  
  //Filter Fields
  var $_date_min	 	= null;
  var $_date_max 		= null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'materiel';
    $spec->key   = 'materiel_id';
    return $spec;
  }

  function getBackProps() {
      $backProps = parent::getBackProps();
      $backProps["refMateriel"] = "CRefMateriel materiel_id";
      $backProps["stock"] = "CStock materiel_id";
     return $backProps;
  }
  
  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "nom"         => "str notNull maxLength|50 seekable",
      "code_barre"  => "num",
      "description" => "text seekable",
      "category_id" => "ref notNull class|CCategory",
      "_date_min" 	   => "date",
      "_date_max" 	   => "date moreEquals|_date_min",
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
  
  function loadRefsBack(){
    $this->_ref_stock = new CStock;
    $where = array();
    $where["materiel_id"] = "= '$this->materiel_id'";
    $this->_ref_stock = $this->_ref_stock->loadList($where);
      
    $this->_ref_refMateriel = new CRefMateriel;
    $where = array();
    $where["materiel_id"] = "= '$this->materiel_id'";
    $this->_ref_refMateriel = $this->_ref_refMateriel->loadList($where);
  } 
  
  function loadRefsFwd(){ 
    $this->_ref_category = new CCategory;
    $this->_ref_category->load($this->category_id);  
  }
  
  function getPerm($permType) {
    if(!$this->_ref_category) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_category->getPerm($permType));
  }
  
}
?>