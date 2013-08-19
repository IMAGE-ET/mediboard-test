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

/**
 * Lien entre un séjour et des prestations
 */
class CItemLiaison extends CMbObject{
  // DB Table key
  public $item_liaison_id;
  
  // DB Fields
  public $sejour_id;
  public $item_souhait_id;
  public $item_realise_id;
  public $date;
  public $quantite;
  
  /** @var CAffectation */
  public $_ref_affectation;

  /** @var CItemPrestation */
  public $_ref_item;

  /** @var CItemPrestation */
  public $_ref_item_realise;

  /** @var CSejour */
  public $_ref_sejour;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "item_liaison";
    $spec->key   = "item_liaison_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"]       = "ref notNull class|CSejour cascade";
    $specs["item_souhait_id"] = "ref class|CItemPrestation cascade";
    $specs["item_realise_id"] = "ref class|CItemPrestation cascade";
    $specs["date"]            = "date";
    $specs["quantite"]        = "num default|0";
    
    return $specs;
  }

  /**
   * Charge l'item de prestation souhaité
   *
   * @return CItemPrestation
   */
  function loadRefItem() {
    return $this->_ref_item = $this->loadFwdRef("item_souhait_id", true);
  }

  /**
   * Charge l'item de prestation réalisé
   *
   * @return CItemPrestation
   */
  function loadRefItemRealise() {
    return $this->_ref_item_realise = $this->loadFwdRef("item_realise_id", true);
  }

  /**
   * Charge le séjour
   *
   * @return CSejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }
}