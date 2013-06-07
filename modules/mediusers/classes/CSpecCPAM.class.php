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
 * The CDiscipline Class
 */
class CSpecCPAM extends CMbObject {
  public $spec_cpam_id;

  // DB Fields
  public $text;
  public $actes;

  /** @var CMediusers[] */
  public $_ref_users;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'spec_cpam';
    $spec->key   = 'spec_cpam_id';
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["users"] = "CMediusers spec_cpam_id";
    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["text"]  = "str notNull seekable";
    $specs["actes"] = "str notNull";
    return $specs;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields () {
    parent::updateFormFields();
    $this->_view = $this->_id.' - '.strtolower($this->text);
    $this->_shortview = CMbString::truncate($this->_view);
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    $where = array(
      "spec_cpam_id" => "= '$this->spec_cpam_id'",
    );

    $this->_ref_users = new CMediusers();
    $this->_ref_users = $this->_ref_users->loadList($where);
  }
}
