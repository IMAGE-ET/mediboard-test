<?php /** $Id: CItemPrestation.class.php $ **/

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  
  // Ref Fields
  public $_ref_affectation;
  public $_ref_item;
  public $_ref_item_realise;

  public $_ref_sejour;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "item_liaison";
    $spec->key   = "item_liaison_id";
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"]       = "ref notNull class|CSejour cascade";
    $specs["item_souhait_id"] = "ref class|CItemPrestation cascade";
    $specs["item_realise_id"] = "ref class|CItemPrestation cascade";
    $specs["date"]            = "date";
    $specs["quantite"]        = "num default|0";
    
    return $specs;
  }
  
  function loadRefItem() {
    return $this->_ref_item = $this->loadFwdRef("item_souhait_id", true);
  }
  
  function loadRefItemRealise() {
    return $this->_ref_item_realise = $this->loadFwdRef("item_realise_id", true);
  }
  
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }
}