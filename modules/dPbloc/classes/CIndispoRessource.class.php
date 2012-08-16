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

class CIndispoRessource extends CMbObject{
  // DB Table Key
  var $indispo_ressource_id = null;
  
  // DB References
  var $ressource_materielle_id    = null;
  
  // DB Fields
  var $deb                        = null;
  var $fin                        = null;
  var $commentaire                = null;
  
  // Ref Fields
  var $_ref_ressource_materielle  = null;
  
  // Form Fields
  var $_debut_offset              = null;
  var $_fin_offset                = null;
  var $_width                     = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'indispo_ressource';
    $spec->key   = 'indispo_ressource_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    
    $specs["ressource_materielle_id"]    = "ref notNull class|CRessourceMaterielle autocomplete|libelle";
    $specs["deb"]                        = "dateTime notNull";
    $specs["fin"]                        = "dateTime notNull";
    $specs["commentaire"]                = "text helped";
    
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "Indisponibilité du " . mbDateToLocale($this->deb);
    if ($this->deb != $this->fin) {
      $this->_view .= " au " . mbDateToLocale($this->fin);
    }
  }
  
  function loadRefRessource() {
    return $this->_ref_ressource_materielle = $this->loadFwdRef("ressource_materielle_id");
  }
}
