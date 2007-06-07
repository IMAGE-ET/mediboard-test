<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

class CExamenLabo extends CMbObject {
  // DB Table key
  var $examen_labo_id = null;
  
  // DB References
  var $catalogue_labo_id = null;
  
  // DB fields
  var $identifiant = null;
  var $libelle     = null;
  var $type        = null;
  var $min         = null;
  var $max         = null;
  var $unite       = null;
  
  var $deb_application = null;
  var $fin_application = null;
  var $realisateur     = null;
  var $applicabilite   = null;
  var $age_min         = null;
  var $age_max         = null;
  
  var $type_prelevement     = null;
  var $methode_prelevement  = null;
  var $quantite_prelevement = null;
  var $unite_prelevement    = null;

  var $conservation       = null;
  var $temps_conservation = null;
  
  var $duree_execution    = null;
  var $execution_lun = null;
  var $execution_mar = null;
  var $execution_mer = null;
  var $execution_jeu = null;
  var $execution_ven = null;
  var $execution_sam = null;
  var $execution_dim = null;
  
  var $technique       = null;
  var $materiel        = null;
  var $remarques       = null;

  // Form fields
  var $_interne = null;
  
  // Fwd References
  var $_ref_catalogue_labo = null;
  var $_ref_realisateur    = null;
  
  // Back References
  var $_ref_items_pack_labo = null;

  // Distant References
  var $_ref_packs_labo = null;
  var $_ref_catalogues = null;
  
  function CExamenLabo() {
    $this->CMbObject("examen_labo", "examen_labo_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "catalogue_labo_id"    => "ref class|CCatalogueLabo notNull",
      "identifiant"          => "str maxLength|10 notNull",
      "libelle"              => "str notNull",
      "type"                 => "enum list|bool|num|str notNull",
      "unite"                => "str maxLength|12",
      "min"                  => "float",
      "max"                  => "float moreThan|min",
      "deb_application"      => "date",
      "fin_application"      => "date moreThan|deb_application",
      "applicabilite"        => "enum list|homme|femme|unisexe",
      "realisateur"          => "ref class|CMediusers",
      "age_min"              => "num pos",
      "age_max"              => "num pos moreThan|age_min",
      "technique"            => "text",
      "materiel"             => "text",
      "type_prelevement"     => "enum list|sang|urine|biopsie",
      "methode_prelevement"  => "text",
      "quantite_prelevement" => "float",
      "unite_prelevement"    => "str maxLength|12",
      "conservation"         => "text",
      "temps_conservation"   => "num pos",
      "execution_lun"        => "bool",
      "execution_mar"        => "bool",
      "execution_mer"        => "bool",
      "execution_jeu"        => "bool",
      "execution_ven"        => "bool",
      "execution_sam"        => "bool",
      "execution_dim"        => "bool",
      "duree_execution"      => "num pos",
      "remarques"            => "text",
    );
  }
  
  function check() {
    parent::check();
  }
  
  function getSiblings() {
    mbTrace($$this->getProps());
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["items_pack_labo"] = "CPackItemExamenLabo examen_labo_id";
    $backRefs["prescriptions"] = "CPrescriptionLaboExamen examen_labo_id";
    return $backRefs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->identifiant;
    $this->_view = "$this->identifiant : $this->libelle ($this->type_prelevement)";
        
    $this->_interne = 1;
  }
  
  function loadRefsFwd() {
    $this->_ref_catalogue_labo = new CCatalogueLabo;
    $this->_ref_catalogue_labo->load($this->catalogue_labo_id);
    
    $this->_ref_realisateur = new CMediusers;
    $this->_ref_realisateur->load($this->realisateur);
  }

  function loadView() {
    parent::loadView();
    $this->loadClassification();
  }

  function loadComplete() {
    parent::loadComplete();
    $this->loadClassification();
    $this->loadRealisateurDeep();
  }
  
  /**
   * Load realisateur and associated function and group
   */
  function loadRealisateurDeep() {
    $realisateur =& $this->_ref_realisateur; 
    if ($realisateur->_id) {
      $realisateur->loadRefFunction();
      $function =& $realisateur->_ref_function;
      $function->loadRefsFwd();
    }
  }
  /**
   * Load complete catalogue classification
   */
  function loadClassification() {
    $catalogue = $this->_ref_catalogue_labo;
    $catalogues = array();
    $catalogues[$catalogue->_id] = $catalogue;
    while ($parent_id = $catalogue->pere_id) {
      $catalogue = new CCatalogueLabo;
      $catalogue->load($parent_id);
      $catalogues[$catalogue->_id] = $catalogue;
    }
    
    $level = count($catalogues);
    foreach ($catalogues as &$catalogue) {
      $catalogue->_level = --$level;
    }

    $this->_ref_catalogues = array_reverse($catalogues, true);
  }
  
  function loadRefsBack() {
    $this->_ref_packs_labo = array();
    $item = new CPackItemExamenLabo;
    $item->examen_labo_id = $this->_id;
    $this->_ref_items_pack_labo = $item->loadMatchingList();
    
    foreach ($this->_ref_items_pack_labo as &$item) {
      $item->loadRefPack();
      $pack =& $item->_ref_pack_examens_labo;
      $this->_ref_packs_labo[$pack->_id] = $pack;
    }
  }
}

?>