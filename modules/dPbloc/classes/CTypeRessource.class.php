<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Types de ressource materielles utilisées au bloc opératoire
 * Class CTypeRessource
 */
class CTypeRessource extends CMbObject{
  public $type_ressource_id;
  
  // DB References
  public $group_id;
  
  // DB Fields
  public $libelle;
  public $description;
  
  /** @var CRessourceMaterielle */
  public $_ref_ressources;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'type_ressource';
    $spec->key   = 'type_ressource_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref notNull class|CGroups";
    $props["libelle"]     = "str notNull seekable";
    $props["description"] = "text helped";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["ressources_materielles"]  = "CRessourceMaterielle type_ressource_id";
    $backProps["besoins"] = "CBesoinRessource type_ressource_id";
    
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->libelle;
  }

  /**
   * Chargement des ressources materielles correspondantes
   *
   * @return CRessourceMaterielle[]
   */
  function loadRefsRessources() {
    return $this->_ref_ressources = $this->loadBackRefs("ressources_materielles");
  }
}
