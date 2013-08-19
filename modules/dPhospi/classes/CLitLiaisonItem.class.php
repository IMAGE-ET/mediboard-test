<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CLitLiaisonItem extends CMbObject {
  // DB Table key
  public $lit_liaison_item_id;
  
  // DB Fields
  public $lit_id;
  public $item_prestation_id;
  
  /** @var CLit */
  public $_ref_lit;

  /** @var CItemPrestation */
  public $_ref_item_prestation;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "lit_liaison_item";
    $spec->key   = "lit_liaison_item_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["lit_id"]     = "ref notNull class|CLit cascade";
    $specs["item_prestation_id"] = "ref notNull class|CItemPrestation cascade";
    
    return $specs;
  }
  
  function loadRefLit() {
    return $this->_ref_lit = $this->loadFwdRef("lit_id", true);
  }
  
  function loadRefItemPrestation() {
    return $this->_ref_item_prestation = $this->loadFwdRef("item_prestation_id", true);
  }
}
