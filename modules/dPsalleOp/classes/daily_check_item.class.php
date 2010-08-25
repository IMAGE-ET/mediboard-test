<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDailyCheckItem extends CMbObject {
  var $daily_check_item_id  = null;

  // DB Fields
  var $list_id      = null;
  var $item_type_id = null;
  var $checked = null;
  
  // Refs
  var $_ref_list = null;
  var $_ref_item_type = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'daily_check_item';
    $spec->key   = 'daily_check_item_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['list_id']      = 'ref notNull class|CDailyCheckList';
    $specs['item_type_id'] = 'ref notNull class|CDailyCheckItemType';
    $specs['checked']      = 'enum notNull list|yes|no|nr|na';
    return $specs;
  }
  
  function getAnswer(){
    /* $this->loadRefsFwd();
    
    switch ($this->_ref_item_type->attribute) {
      default:
      case "normal":         $value = ($this->checked === null ? 'Unknown' : ($this->checked != 0 ? 'Yes' : 'No')); break;
      case "notrecommended": $value = ($this->checked === null ? 'N/R' :     ($this->checked != 0 ? 'Yes' : 'No')); break;
      case "notapplicable":  $value = ($this->checked === null ? 'Unknown' : ($this->checked != 0 ? 'Yes' : 'N/A')); break;
    }*/
    
    return $this->getFormattedValue("checked");
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->_ref_item_type (".$this->getAnswer().")";
  }
  
  function loadRefsFwd() {
    $this->_ref_list = $this->loadFwdRef("list_id", true);
    $this->_ref_item_type = $this->loadFwdRef("item_type_id", true);
  }
}
?>