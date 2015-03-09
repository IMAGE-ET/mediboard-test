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
 * Secteur d'établissement, regroupe des services
 */
class CSecteur extends CInternalStructure {
  // DB Table key
  public $secteur_id;
  
  // DB references
  public $group_id;

  // DB Fields
  public $nom;
  public $description;
  
  /** @var CService[] */
  public $_ref_services;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'secteur';
    $spec->key   = 'secteur_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["services"] = "CService secteur_id";
    
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref notNull class|CGroups";
    $props["nom"]         = "str notNull";
    $props["description"] = "text seekable";
    
    return $props;
  }

  /**
   * Charge les services
   *
   * @return CService[]
   */
  function loadRefsServices() {
    return $this->_ref_services = $this->loadBackRefs("services", "nom");
  }

  /**
   * @see parent::mapEntityTo()
   */
  function mapEntityTo () {
    $this->_name = $this->nom;
  }

  /**
   * @see parent::mapEntityFrom()
   */
  function mapEntityFrom () {
    if ($this->_name != null) {
      $this->nom = $this->_name;
    }
  }
}