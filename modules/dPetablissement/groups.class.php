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
  var $_ref_blocs = null;
  var $_ref_produits_livret = null;
  static $_ref_current = null;
  var $_ref_dmi_categories = null;
  var $_ref_services = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'groups_mediboard';
    $spec->key   = 'group_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["documents_ged"]           = "CDocGed group_id";
    $backProps["functions"]               = "CFunctions group_id";
    $backProps["menus"]                   = "CMenu group_id";
    $backProps["plats"]                   = "CPlat group_id";
    $backProps["blocs"]                   = "CBlocOperatoire group_id";
    $backProps["sejours"]                 = "CSejour group_id";
    $backProps["services"]                = "CService group_id";
    $backProps["stocks"]                  = "CStock group_id";
    $backProps["types_repas"]             = "CTypeRepas group_id";
    $backProps["modeles"]                 = "CCompteRendu group_id";
	  $backProps["chapitres_qualite"]       = "CChapitreDoc group_id";
	  $backProps["themes_qualite"]          = "CThemeDoc group_id";
	  $backProps["prestations"]             = "CPrestation group_id";
	  $backProps["product_orders"]          = "CProductOrder group_id";
	  $backProps["product_stocks"]          = "CProductStockGroup group_id";
	  $backProps["protocoles_prescription"] = "CPrescription group_id";
	  $backProps["etablissements_sherpa"]   = "CSpEtablissement group_id";
    $backProps["dmi_categories"]          = "CDMICategory group_id";
    $backProps["categories_prescription"] = "CCategoryPrescription group_id";
	  
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["text"]                = "str notNull confidential seekable";
    $specs["raison_sociale"]      = "str maxLength|50";
    $specs["adresse"]             = "text confidential";
    $specs["cp"]                  = "numchar length|5";
    $specs["ville"]               = "str maxLength|50 confidential";
    $specs["tel"]                 = "numchar length|10 mask|99S99S99S99S99";
    $specs["fax"]                 = "numchar length|10 mask|99S99S99S99S99";
    $specs["tel_anesth"]          = "numchar length|10 mask|99S99S99S99S99";
    $specs["service_urgences_id"] = "ref class|CFunctions";
    $specs["directeur"]           = "str maxLength|50";
    $specs["domiciliation"]       = "str maxLength|9";
    $specs["siret"]               = "str length|14";
    $specs["ape"]                 = "str maxLength|6 confidential";
    $specs["mail"]                = "email";
    $specs["web"]                 = "str";
    return $specs;
  }
  
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->text;
    if(strlen($this->text) > 25)
      $this->_shortview = substr($this->text, 0, 23)."...";
    else
      $this->_shortview = $this->text;
  }
  
  function loadRefLivretTherapeutique($lettre = "%", $limit = 50, $full_mode = true){
    global $g;
    if ($lettre != '%') {
	    $produit = new CBcbProduit();
	    
	    // Chargement des produits du livret Therapeutique en fonction d'une lettre
	    $produits = $produit->searchProduit($lettre, 1, "debut", 0, $limit, $g, $full_mode);
	    
	    $this->_ref_produits_livret = array();
	    foreach($produits as $code_cip => $prod){
	      $produitLivretTherapeutique = new CBcbProduitLivretTherapeutique();
	      $produitLivretTherapeutique->load($code_cip);
	      $produitLivretTherapeutique->_ref_produit = $prod;
	      $this->_ref_produits_livret[] = $produitLivretTherapeutique;
	    }
    } else {
  	  $this->_ref_produits_livret = CBcbProduitLivretTherapeutique::getProduits('CODECIP', $limit, $full_mode);
    }
  }

  /**
   * Load functions with given permission
   */
  function loadFunctions($permType = PERM_READ) {
    return $this->_ref_functions = CMediusers::loadFonctions($permType, $this->_id);
  }
  
  /**
   * Load blocs operatoires with given permission
   */
  function loadBlocs($permType = PERM_READ, $load_salles = true) {
  	$bloc = new CBlocOperatoire();
  	$where = array('group_id' => "='$this->_id'");
    $this->_ref_blocs = $bloc->loadListWithPerms($permType, $where, 'nom');
    
    if ($load_salles) {
			foreach ($this->_ref_blocs as &$bloc) {
			  $bloc->loadRefsSalles();
			}
    }
		return $this->_ref_blocs;
  }
  
  function loadRefsBack() {
    $this->loadFunctions();
  }

  function loadRefsService(){
    $this->_ref_services = $this->loadBackRefs("services");
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
    $template->addProperty("Etablissement - Nom"       , $this->text);
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
    if (!self::$_ref_current) {
	    self::$_ref_current = new CGroups();
	    self::$_ref_current->load($g);
    }
    return self::$_ref_current;
  }
  
  function loadRefsDMICategories() {
    $this->_ref_dmi_categories = $this->loadBackRefs("dmi_categories", "nom");
  }
}
?>