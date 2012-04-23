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
  var $type_ressource_id          = null;
  var $protocole_id               = null;
  var $operation_id               = null;
  var $commentaire                = null;
  
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
    $specs["operation_id"]      = "ref class|COperation notNull";
    $specs["protocole_id"]      = "ref class|CProtocole notNull";
    $specs["commentaire"]       = "text helped";
    
    return $specs;
  }
}
