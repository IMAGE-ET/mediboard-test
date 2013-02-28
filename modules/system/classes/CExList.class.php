<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CExList extends CExListItemsOwner {
  public $ex_list_id;
  
  public $name;
  public $coded;
  public $multiple;

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
    $props["multiple"] = "bool default|0 show|0";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["concepts"] = "CExConcept ex_list_id";
    $backProps["list_items"] = "CExListItem list_id";
    return $backProps;
  }
  
  function loadView(){
    parent::loadView();
    $this->loadBackRefs("concepts");
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view  = $this->coded ? "# " : "";
    $this->_view .= $this->name;
  }
}
