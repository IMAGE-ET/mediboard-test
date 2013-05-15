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

class CTypeRessource extends CMbObject{
  public $type_ressource_id;
  
  // DB References
  public $group_id;
  
  // DB Fields
  public $libelle;
  public $description;
  
  /** @var CRessourceMaterielle */
  public $_ref_ressources;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'type_ressource';
    $spec->key   = 'type_ressource_id';
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref notNull class|CGroups";
    $props["libelle"]     = "str notNull seekable";
    $props["description"] = "text helped";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["ressources_materielles"]  = "CRessourceMaterielle type_ressource_id";
    $backProps["besoins"] = "CBesoinRessource type_ressource_id";
    
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->libelle;
  }

  /**
   * @return CRessourceMaterielle[]
   */
  function loadRefsRessources() {
    return $this->_ref_ressources = $this->loadBackRefs("ressources_materielles");
  }
}
