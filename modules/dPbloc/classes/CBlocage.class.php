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

class CBlocage extends CMbObject {
  // DB Table Key
  var $blocage_id = null;
  
  // DB References
  var $salle_id   = null;
  
  // DB Fields
  var $libelle    = null;
  var $deb        = null;
  var $fin        = null;
  
  // References
  var $_ref_salle = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'blocage';
    $spec->key   = 'blocage_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs["salle_id"] = "ref class|CSalle notNull";
    $specs["libelle"]  = "str seekable";
    $specs["deb"]      = "date notNull";
    $specs["fin"]      = "date notNull moreEquals|deb";
    
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view = "Blocage du " . $this->getFormattedValue("deb") . " au " . $this->getFormattedValue("fin");
  }
  
  function loadRefSalle() {
    return $this->_ref_salle = $this->loadFwdRef("salle_id", true);
  }
}

?>