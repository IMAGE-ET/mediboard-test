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

class CCatalogueLabo extends CMbObject {
  // DB Table key
  public $catalogue_labo_id;

  // DB References
  public $pere_id;

  // DB fields
  public $identifiant;
  public $libelle;
  public $function_id;
  public $obsolete;

  /** @var CCatalogueLabo */
  public $_ref_pere;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CExamenLabo[] */
  public $_ref_examens_labo;

  /** @var CCatalogueLabo[] */
  public $_ref_catalogues_labo;

  // Distant references
  public $_ref_prescription_items;
  public $_count_examens_labo;
  public $_total_examens_labo;

  // Form fields
  public $_level;

  /**
   * @see parent::_construct()
   */
  function __construct() {
    parent::__construct();
    $this->_locked =& $this->_external;
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'catalogue_labo';
    $spec->key   = 'catalogue_labo_id';
    return $spec;
  }

  /**
   * @see parent::check()
   */
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }

    if ($this->hasAncestor($this)) {
      return "Cyclic catalog creation";
    }

    // Checks whether there is a sibling catalogue in the same hierarchy
    $root = $this->getRootCatalogue();
    foreach ($this->getSiblings() as $_sibling) {
      $_root = $_sibling->getRootCatalogue();
      if ($root->_id == $_root->_id) {
        return "CCatalogue-sibling-conflict ($this->identifiant)";
      }
    }

    return null;
  }

  /**
   * Recursive root catalogue accessor
   *
   * @return self
   */
  function getRootCatalogue() {
    if (!$this->pere_id) {
      return $this;
    }

    $this->loadParent();
    return $this->_ref_pere->getRootCatalogue();
  }

  /**
   * load catalogues with same identifier
   *
   * @return CCatalogueLabo[]
   */
  function getSiblings() {
    $catalogue = new CCatalogueLabo;
    $where = array();
    $where["identifiant"] = "= '$this->identifiant'";
    $where["catalogue_labo_id"] = "!= '$this->catalogue_labo_id'";
    return $catalogue->loadList($where);
  }

  /**
   * Checks whether given catalogue is an ancestor
   *
   * @param self $catalogue The catalogue to check against
   *
   * @return boolean
   */
  function hasAncestor($catalogue) {
    if (!$this->_id) {
      return false;
    }

    if ($catalogue->_id == $this->pere_id) {
      return true;
    }

    $this->loadParent();
    return $this->_ref_pere->hasAncestor($catalogue);
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["pere_id"]     = "ref class|CCatalogueLabo";
    $props["function_id"] = "ref class|CFunctions";
    $props["identifiant"] = "str maxLength|10 notNull";
    $props["libelle"]     = "str notNull";
    $props["obsolete"]    = "bool";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["catalogues_labo"] = "CCatalogueLabo pere_id";
    $backProps["examens_labo"]    = "CExamenLabo catalogue_labo_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_shortview = $this->identifiant;
    $this->_view = $this->libelle;
  }

  /**
   * Compute self level
   *
   * @return int
   */
  function computeLevel() {
    if (!$this->pere_id) {
      return $this->_level = 0;
    }

    $this->loadParent();
    return $this->_level = $this->_ref_pere->computeLevel() + 1;
  }

  /**
   * Load parent catalog
   *
   * @return void
   */
  function loadParent() {
    if (!$this->_ref_pere) {
      $this->_ref_pere = new CCatalogueLabo;
      $this->_ref_pere->load($this->pere_id);
    }
  }

  function loadRefFunction() {
    if (!$this->_ref_function) {
      $this->_ref_function = new CFunctions();
      $this->_ref_function->load($this->function_id);
    }
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadParent();
    $this->loadRefFunction();
  }

  function loadSections() {
    $this->_ref_catalogues_labo = $this->loadBackRefs("catalogues_labo", "libelle");
  }

  function loadExamens() {
    $this->_ref_examens_labo = $this->loadBackRefs("examens_labo");
  }

  function loadSectionsWithoutObsolete(){
    $catalogueLabo = new CCatalogueLabo();
    $catalogueLabo->pere_id = $this->_id;
    $catalogueLabo->obsolete = 0;
    $this->_ref_catalogues_labo = $catalogueLabo->loadMatchingList();
  }

  function loadExamensWithoutObsolete(){
    $examenLabo = new CExamenLabo();
    $examenLabo->catalogue_labo_id = $this->_id;
    $examenLabo->obsolete = 0;
    $this->_ref_examens_labo = $examenLabo->loadMatchingList();
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();

    $this->loadSectionsWithoutObsolete();
    $this->loadExamensWithoutObsolete();
  }

  function loadRefsDeep($n = 0) {
    $this->_level = $n;
    $this->loadParent();
    $this->loadExternal();
    $this->_count_examens_labo = $this->countBackRefs("examens_labo");
    $this->_total_examens_labo = $this->_count_examens_labo;

    $this->loadSectionsWithoutObsolete();

    foreach ($this->_ref_catalogues_labo as &$_catalogue) {
      $_catalogue->_ref_pere =& $this;
      $_catalogue->loadRefsDeep($this->_level + 1);
      $this->_total_examens_labo += $_catalogue->_total_examens_labo;
    }
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($perm_type) {
    if ($this->function_id && !$this->pere_id) {
      $this->loadRefFunction();
      return $this->_ref_function->getPerm($perm_type);
    }
    elseif ($this->pere_id) {
      $this->loadParent();
      return $this->_ref_pere->getPerm($perm_type);
    }

    return true;
  }
}
