<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Qualite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Chapitre des documents qualité
 * Class CChapitreDoc
 */
class CChapitreDoc extends CMbObject {
  // DB Table key
  public $doc_chapitre_id;
    
  // DB Fields
  public $pere_id;
  public $group_id;
  public $nom;
  public $code;
  
  // Fwd refs
  /** @var CChapitreDoc */
  public $_ref_pere;
  /** @var  CGroups */
  public $_ref_group;
  
  // Back Refs
  /** @var  CChapitreDoc[] */
  public $_ref_chapitres_doc;
  
  // Other fields
  public $_level;
  public $_path;
  public $_chaps_and_subchaps;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'doc_chapitres';
    $spec->key   = 'doc_chapitre_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["chapitres_doc"] = "CChapitreDoc pere_id";
    $backProps["chapitres_ged"] = "CDocGed doc_chapitre_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["pere_id"]  = "ref class|CChapitreDoc";
    $specs["group_id"] = "ref class|CGroups";
    $specs["nom"]      = "str notNull maxLength|50";
    $specs["code"]     = "str notNull maxLength|10";
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "[$this->code] $this->nom";
    $this->_shortview = $this->code; 
  }
  
  function loadParent() {
    if (!$this->_ref_pere) {
      $this->_ref_pere = new CChapitreDoc;
      $this->_ref_pere->load($this->pere_id);
    }
  }
  
  function loadRefGroup() {
    if (!$this->_ref_group) {
      $this->_ref_group = new CGroups();
      $this->_ref_group->load($this->group_id);
    }
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadParent();
    $this->loadRefGroup();
  }
  
  function computeLevel() {
    if (!$this->pere_id) {
      return $this->_level = 0;
    }
    
    $this->loadParent();
    return $this->_level = $this->_ref_pere->computeLevel() + 1;
  }
  
  function computePath() {
    if (!$this->pere_id) {
      return $this->_path = "$this->code-";
    }
    
    $this->loadParent();
    return $this->_path = $this->_ref_pere->computePath().$this->code."-";
  }

  function loadSections() {
    $this->_ref_chapitres_doc = $this->loadBackRefs("chapitres_doc", "code");
  }
  
  function loadChapsDeep($n = 0) {
    $this->_chaps_and_subchaps = array($this->_id);
    $this->_level = $n;
    if (CAppUI::conf("dPqualite CChapitreDoc profondeur") > ($this->_level + 1)) {
      $this->loadSections();
      foreach ($this->_ref_chapitres_doc as &$_chapitre) {
        $_chapitre->_ref_pere =& $this;
        $this->_chaps_and_subchaps = array_merge($this->_chaps_and_subchaps, $_chapitre->loadChapsDeep($this->_level + 1));
      }
    }
    return $this->_chaps_and_subchaps;
  }
}
