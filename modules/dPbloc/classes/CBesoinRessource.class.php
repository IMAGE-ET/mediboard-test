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

class CBesoinRessource extends CMbObject{
  // DB Table Key
  var $besoin_ressource_id = null;
  
  // DB References
  var $type_ressource_id   = null;
  var $protocole_id        = null;
  var $operation_id        = null;
  var $commentaire         = null;
  
  // Ref Fields
  var $_ref_type_ressource = null;
  var $_ref_operation      = null;
  var $_ref_protocole      = null;
  var $_ref_usage          = null;
  
  // Form Fields
  var $_color              = null;
  var $_width              = null;
  var $_debut_offset       = null;
  var $_fin_offset         = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'besoin_ressource';
    $spec->key   = 'besoin_ressource_id';
    
    $spec->xor["owner"] = array("operation_id", "protocole_id");
    
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs["type_ressource_id"] = "ref class|CTypeRessource notNull";
    $specs["operation_id"]      = "ref class|COperation";
    $specs["protocole_id"]      = "ref class|CProtocole";
    $specs["commentaire"]       = "text helped";
    
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["usages"] = "CUsageRessource besoin_ressource_id";
    
    return $backProps;
  }
  
  function loadRefTypeRessource() {
    return $this->_ref_type_ressource = $this->loadFwdRef("type_ressource_id", true);
  }
  
  function loadRefOperation() {
    return $this->_ref_operation = $this->loadFwdRef("operation_id", true);
  }
  
  function loadRefProtocole() {
    return $this->_ref_protocole = $this->loadFwdRef("protocole_id", true);
  }
  
  function loadRefUsage() {
    return $this->_ref_usage = $this->loadUniqueBackRef("usages", true);
  }
}
