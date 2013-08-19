<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Type de prestation
 */
class CPrestation extends CMbObject {
  // DB Table key
  public $prestation_id;
  
  // DB references
  public $group_id;
  
  // DB fields
  public $nom;
  public $code;
  public $description;

  /** @var CGroups */
  public $_ref_group;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prestation';
    $spec->key   = 'prestation_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["sejours"]  = "CSejour prestation_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps(){
    $specs = parent::getProps();
    $specs["group_id"]    = "ref notNull class|CGroups";
    $specs["nom"]         = "str notNull seekable";
    $specs["code"]        = "str maxLength|12 seekable";
    $specs["description"] = "text confidential seekable";
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  /**
   * Charge l'établissement
   *
   * @return CGroups
   */
  function loadRefGroup() {
    return $this->_ref_group = $this->loadFwdRef("group_id", true);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd(){
    $this->loadRefGroup();
  }
  
  /**
   * Niveaux de prestations pour l'établissement courant
   *
   * @return self[]
   */
  static function loadCurrentList() {
    $prestation = new self();
    $prestation->group_id = CGroups::loadCurrent()->_id;;
    return $prestation->loadMatchingList("nom");    
  }
}

