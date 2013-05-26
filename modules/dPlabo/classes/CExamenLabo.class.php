<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Labo
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CExamenLabo extends CMbObject {
  // DB Table key
  public $examen_labo_id;

  // DB References
  public $catalogue_labo_id;

  // DB fields
  public $identifiant;
  public $libelle;
  public $type;
  public $min;
  public $max;
  public $unite;

  public $deb_application;
  public $fin_application;
  public $realisateur;
  public $applicabilite;
  public $age_min;
  public $age_max;

  public $type_prelevement;
  public $methode_prelevement;
  public $quantite_prelevement;
  public $unite_prelevement;

  public $conservation;
  public $temps_conservation;

  public $duree_execution;
  public $execution_lun;
  public $execution_mar;
  public $execution_mer;
  public $execution_jeu;
  public $execution_ven;
  public $execution_sam;
  public $execution_dim;

  public $technique;
  public $materiel;
  public $remarques;
  public $obsolete;

  /** @var CCatalogueLabo */
  public $_ref_catalogue_labo;

  /** @var CMediusers */
  public $_ref_realisateur;

  /** @var CPackItemExamenLabo[] */
  public $_ref_items_pack_labo;

  /** @var CPackExamensLabo[] */
  public $_ref_packs_labo;

  /** @var CCatalogueLabo[] */
  public $_ref_catalogues;

  /** @var self[] */
  public $_ref_siblings;

  /** @var CCatalogueLabo */
  public $_ref_root_catalogue;

  function __construct() {
    parent::__construct();
    $this->_locked =& $this->_external;
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'examen_labo';
    $spec->key   = 'examen_labo_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specsParent = parent::getProps();
    $specs = array (
      "catalogue_labo_id"    => "ref class|CCatalogueLabo notNull",
      "identifiant"          => "str maxLength|10 notNull",
      "libelle"              => "str notNull",
      "type"                 => "enum list|bool|num|str notNull",
      "unite"                => "str maxLength|12",
      "min"                  => "float",
      "max"                  => "float moreThan|min",
      "deb_application"      => "date",
      "fin_application"      => "date moreThan|deb_application",
      "applicabilite"        => "enum list|homme|femme|unisexe default|unisexe",
      "realisateur"          => "ref class|CMediusers",
      "age_min"              => "num pos",
      "age_max"              => "num pos moreThan|age_min",
      "technique"            => "text",
      "materiel"             => "text",
      "type_prelevement"     => "enum list|sang|urine|biopsie default|sang",
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
      "duree_execution"      => "num",
      "remarques"            => "text",
      "obsolete"             => "bool"
    );
    return array_merge($specsParent, $specs);
  }

  /**
   * @see parent::check()
   */
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }

    // Checks whether there is a sibling examen in the same hierarchy
    $root = $this->getRootCatalogue();
    foreach ($this->getSiblings() as $_sibling) {
      $_root = $_sibling->getRootCatalogue();
      if ($root->_id == $_root->_id) {
        return "CExamenLabo-sibling-conflict";
      }
    }

    return null;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["items_pack_labo"] = "CPackItemExamenLabo examen_labo_id";
    $backProps["prescriptions"] = "CPrescriptionLaboExamen examen_labo_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->identifiant;
    $this->_view = "[$this->identifiant] $this->libelle ($this->type_prelevement)";
  }

  function loadCatalogue() {
    if (!$this->_ref_catalogue_labo) {
      $this->_ref_catalogue_labo = new CCatalogueLabo;
      $this->_ref_catalogue_labo->load($this->catalogue_labo_id);
    }
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadCatalogue();
    $this->_ref_realisateur = new CMediusers;
    $this->_ref_realisateur->load($this->realisateur);
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->loadClassification();
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete() {
    parent::loadComplete();
    $this->loadClassification();
    $this->loadRealisateurDeep();
  }

  /**
   * Recursive root catalogue accessor
   */
  function getRootCatalogue() {
    $this->loadCatalogue();
    return $this->_ref_root_catalogue = $this->_ref_catalogue_labo->getRootCatalogue();
  }

  /**
   * load catalogues with same identifier
   *
   * @return self[]
   */
  function getSiblings() {
    $examen = new self();
    $where = array();
    $where["identifiant"] = "= '$this->identifiant'";
    $where["examen_labo_id"] = "!= '$this->examen_labo_id'";

    return $this->_ref_siblings = $examen->loadList($where);
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
    $this->loadCatalogue();
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

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();

    // Chargement des pack items 
    $item = new CPackItemExamenLabo;
    $item->examen_labo_id = $this->_id;
    $this->_ref_items_pack_labo = $item->loadMatchingList();

    // Chargement des packs correspondant
    $this->_ref_packs_labo = array();
    foreach ($this->_ref_items_pack_labo as &$item) {
      $item->loadRefPack();
      $pack =& $item->_ref_pack_examens_labo;
      $this->_ref_packs_labo[$pack->_id] = $pack;
    }
  }
}
