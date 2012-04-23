<?php

/**
 * dPbloc
 *  
 * @category dPbloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CTypeRessource extends CMbObject{
  // DB Table Key
  var $type_ressource_id = null;
  
  // DB References
  var $group_id    = null;
  
  // DB Fields
  var $libelle     = null;
  var $description = null;
  
  // References
  var $_ref_ressources = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'type_ressource';
    $spec->key   = 'type_ressource_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs["group_id"]    = "ref notNull class|CGroups";
    $specs["libelle"]     = "str notNull seekable";
    $specs["description"] = "text helped";
    
    return $specs;
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
  
  function loadRefsRessources() {
    return $this->_ref_ressources = $this->loadBackRefs("ressources_materielles");
  }
}
