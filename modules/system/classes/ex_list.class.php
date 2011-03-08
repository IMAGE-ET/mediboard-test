<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleClass("system", "ex_list_items_owner");

class CExList extends CExListItemsOwner {
  var $ex_list_id = null;
  
  var $name       = null;
  var $coded      = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_list";
    $spec->key   = "ex_list_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["name"]  = "str notNull seekable";
    $props["coded"] = "bool notNull default|0";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
		$backProps["concepts"] = "CExConcept ex_list_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->name;
  }
}
