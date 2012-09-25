<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Classe CChargePriceIndicator
 *
 * Table type d'activité, mode de traitement
 */
class CChargePriceIndicator extends CMbObject {
  // DB Table key
  var $charge_price_indicator_id = null; 
    
  // DB Table key
  var $code     = null;
  var $type     = null;
  var $group_id = null;
  var $libelle  = null;
  var $actif    = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    
    $spec->table = 'charge_price_indicator';
    $spec->key   = 'charge_price_indicator_id';
    
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
        
    $props["code"]     = "str notNull";
    
    $sejour = new CSejour();
    $props["type"]     = $sejour->_props["type"];
    
    $props["group_id"] = "ref notNull class|CGroups";
    $props["libelle"]  = "str";
    $props["actif"]    = "bool default|0";
    
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["sejours"] = "CSejour charge_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view      = $this->libelle;
    $this->_shortview = $this->code;
  }
}
?>;