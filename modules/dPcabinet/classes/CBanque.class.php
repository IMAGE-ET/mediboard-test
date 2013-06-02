<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Les banques permettent aux règlements d'être regroupés pour produire des borderaux
 */
class CBanque extends CMbObject {
  public $banque_id;

  // DB fields
  public $nom;
  public $description;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'banque';
    $spec->key   = 'banque_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["nom"]         = "str notNull seekable";
    $props["description"] = "str seekable";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['users']      = 'CMediusers banque_id';
    $backProps['reglements'] = 'CReglement banque_id';
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }
}
