<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPetablissement
 *	@version $Revision$
 *  @author Romain Ollivier
*/

/**
 * The CGroups class
 */
class CGroups extends CMbObject {
  // DB Table key
	var $group_id       = null;	

  // DB Fields
	var $text                = null;
  var $raison_sociale      = null;
  var $adresse             = null;
  var $cp                  = null;
  var $ville               = null;
  var $tel                 = null;
  var $fax                 = null;
  var $mail                = null;
  var $web                 = null;
  var $directeur           = null;
  var $domiciliation       = null;
  var $siret               = null;
  var $ape                 = null;
  var $tel_anesth          = null;
  var $service_urgences_id = null;

  // Object References
  var $_ref_functions = null;
  var $_ref_produits_livret = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'groups_mediboard';
    $spec->key   = 'group_id';
    return $spec;
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["documents_ged"] = "CDocGed group_id";
      $backRefs["functions"]     = "CFunctions group_id";
      $backRefs["menus"]         = "CMenu group_id";
      $backRefs["plats"]         = "CPlat group_id";
      $backRefs["salles"]        = "CSalle group_id";
      $backRefs["sejours"]       = "CSejour group_id";
      $backRefs["services"]      = "CService group_id";
      $backRefs["stocks"]        = "CStock group_id";
      $backRefs["type_repas"]    = "CTypeRepas group_id";
     return $backRefs;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "text"                => "notNull str confidential",
      "raison_sociale"      => "str maxLength|50",
      "adresse"             => "text confidential",
      "cp"                  => "numchar length|5",
      "ville"               => "str maxLength|50 confidential",
      "tel"                 => "numchar length|10 mask|99S99S99S99S99",
      "fax"                 => "numchar length|10 mask|99S99S99S99S99",
      "tel_anesth"          => "numchar length|10 mask|99S99S99S99S99",
      "service_urgences_id" => "ref class|CFunctions",
      "directeur"           => "str maxLength|50",
      "domiciliation"       => "str maxLength|9",
      "siret"               => "str length|14",
      "ape"                 => "str maxLength|6 confidential",
      "mail"                => "email",
      "web"                 => "str",
    );
    return array_merge($specsParent, $specs);
  }
  
  function getSeeks() {
    return array (
      "text" => "like"
    );
  }
 
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->text;
    if(strlen($this->text) > 25)
      $this->_shortview = substr($this->text, 0, 23)."...";
    else
      $this->_shortview = $this->text;
  }
  
  function loadRefLivretTherapeutique($lettre = "%", $limit = 50){
    global $g;
    $produit = new CBcbProduit();
    
    $produits = array();
    // Chargement des produits du livret Therapeutique en fonction d'une lettre
    $produits = $produit->searchProduit($lettre, 1, "debut", 0, $limit, $g);
    
    foreach($produits as $key => $prod){
      $produitLivretTherapeutique = new CBcbProduitLivretTherapeutique();
      $produitLivretTherapeutique->load($prod->code_cip);
      $produitLivretTherapeutique->_ref_produit = $prod;
      $this->_ref_produits_livret[] = $produitLivretTherapeutique;
    }
  }

  /**
   * Load functions with given permission
   */
  function loadFunctions($permType = PERM_READ) {
    $this->_ref_functions = CMediusers::loadFonctions($permType, $this->_id);
  }
  
  function loadRefsBack() {
    $this->loadFunctions();
  }

  
  /**
   * Load groups with given permission
   */
  static function loadGroups($permType = PERM_READ) {
    $order = "text";
    $group = new CGroups;
    $groups = $group->loadList(null, $order);

    foreach ($groups as $_id => $group) {
      if (!$group->getPerm($permType)) {
        unset($groups[$_id]);
      }
    }
    
    return $groups;    
  }
  
  function fillLimitedTemplate(&$template) {
    $template->addProperty("Etablissement - Nom"       , $this->text );
    $template->addProperty("Etablissement - Adresse"   , "$this->adresse $this->cp $this->ville");
    $template->addProperty("Etablissement - Téléphone" , $this->tel);
    $template->addProperty("Etablissement - Fax"       , $this->fax);
  }
  
  function fillTemplate(&$template) {
    $this->fillLimitedTemplate($template);
  }
  
  /**
   * Load the current group
   * @return CGroups
   */
  static function loadCurrent() {
    global $g;
    $group = new CGroups();
    $group->load($g);
    return $group;
  }
}
?>