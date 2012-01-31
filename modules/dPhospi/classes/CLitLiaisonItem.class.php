<?php /* $Id: CLitLiaisonItem.class.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CLitLiaisonItem extends CMbObject {
  // DB Table key
  var $lit_liaison_item_id = null;
  
  // DB Fields
  var $lit_id              = null;
  var $item_prestation_id  = null;
  
  // Ref Fields
  var $_ref_lit            = null;
  var $_ref_item_prestation = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "lit_liaison_item";
    $spec->key   = "lit_liaison_item_id";
    return $spec;
  }
  
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
    return $this->_ref_item_prestation = $this->loadFwdRef("item_prestation_id");
  }
}
?>