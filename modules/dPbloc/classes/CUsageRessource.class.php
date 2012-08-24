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

class CUsageRessource extends CMbObject{
  // DB Table Key
  var $usage_ressource_id = null;
  
  // DB References
  var $ressource_materielle_id    = null;
  var $besoin_ressource_id        = null;
  var $commentaire                = null;
  
  // Ref Fields
  var $_ref_ressource             = null;
  
  // Form Fields
  var $_debut_offset              = null;
  var $_fin_offset                = null;
  var $_width                     = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'usage_ressource';
    $spec->key   = 'usage_ressource_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
      
    $specs["ressource_materielle_id"] = "ref class|CRessourceMaterielle notNull";
    $specs["besoin_ressource_id"]     = "ref class|CBesoinRessource notNull";
    $specs["commentaire"]             = "text helped";
    
    return $specs;
  }
  
  function loadRefRessource() {
    return $this->_ref_ressource = $this->loadFwdRef("ressource_materielle_id", true);
  }
  
  function loadRefBesoin() {
    return $this->_ref_besoin = $this->loadFwdRef("besoin_ressource_id", true);
  }
}
