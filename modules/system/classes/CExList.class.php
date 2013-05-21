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

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_list";
    $spec->key   = "ex_list_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["name"]  = "str notNull seekable";
    $props["coded"] = "bool notNull default|0";
    $props["multiple"] = "bool default|0 show|0";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["concepts"] = "CExConcept ex_list_id";
    $backProps["list_items"] = "CExListItem list_id";
    return $backProps;
  }

  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();
    $this->loadBackRefs("concepts");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view  = $this->coded ? "# " : "";
    $this->_view .= $this->name;
  }
}
