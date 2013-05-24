<?php /** $Id:$ **/

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
  public $charge_price_indicator_id;
    
  // DB Table key
  public $code;
  public $type;
  public $type_pec;
  public $group_id;
  public $libelle;
  public $actif;

  /**
   * specs
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'charge_price_indicator';
    $spec->key   = 'charge_price_indicator_id';
    return $spec;
  }

  /**
   * props
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["code"]     = "str notNull";
    
    $sejour = new CSejour();
    $props["type"]     = $sejour->_props["type"];
    $props["type_pec"] = $sejour->_props["type_pec"];
    
    $props["group_id"] = "ref notNull class|CGroups";
    $props["libelle"]  = "str";
    $props["actif"]    = "bool default|0";
    
    return $props;
  }

  /**
   * get back props
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["sejours"] = "CSejour charge_id";
    return $backProps;
  }

  /**
   * updateformfields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_view      = $this->libelle ? $this->libelle : $this->code;
    $this->_shortview = $this->code;
  }
}
