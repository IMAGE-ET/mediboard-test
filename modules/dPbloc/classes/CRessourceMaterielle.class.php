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

class CRessourceMaterielle extends CMbObject {
  // DB Table Key
  var $ressource_materielle_id = null;
  
  // DB References
  var $type_ressource_id    = null;
  var $group_id             = null;
  
  // DB Fields
  var $libelle              = null;
  var $deb_activite         = null;
  var $fin_activite         = null;
  var $retablissement       = null;
  
  // Ref Fields
  var $_ref_type_ressource  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ressource_materielle';
    $spec->key   = 'ressource_materielle_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs['group_id']          = "ref class|CGroups notNull";
    $specs["type_ressource_id"] = "ref class|CTypeRessource notNull";
    $specs["libelle"]           = "str notNull seekable";
    $specs["deb_activite"]      = "date";
    $specs["fin_activite"]      = "date";
    $specs["retablissement"]    = "bool default|0";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["indispos"] = "CIndispoRessource ressource_materielle_id";
    $backProps["usages"]   = "CUsageRessource ressource_materielle_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = $this->libelle;
  }
  
  function loadRefTypeRessource() {
    return $this->_ref_type_ressource = $this->loadFwdRef("type_ressource_id", true);
  }
}
