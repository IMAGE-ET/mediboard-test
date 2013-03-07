<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage mediusers
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CSecondaryFunction Class
 */
class CSecondaryFunction extends CMbObject {
  // DB Table key
  public $secondary_function_id;

  // DB References
  public $function_id;
  public $user_id;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CMediusers */
  public $_ref_user;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'secondary_function';
    $spec->key   = 'secondary_function_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["function_id"] = "ref notNull class|CFunctions";
    $specs["user_id"]     = "ref notNull class|CMediusers cascade";
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->_ref_user->_view." - ".$this->_ref_function->_view;
    $this->_shortview = $this->_ref_user->_shortview." - ".$this->_ref_function->_shortview;
  }

  function loadRefsFwd() {
    $this->loadRefFunction();
    $this->loadRefUser();
  }

  function loadRefFunction() {
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }

  function loadRefUser() {
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
  }
}
