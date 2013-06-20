<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Incident / évenement per-opératoire
 */
class CAnesthPerop extends CMbObject {
  public $anesth_perop_id;

  // DB References
  public $operation_id;

  // DB fields
  public $libelle;
  public $datetime;
  public $incident;

  /** @var COperation */
  public $_ref_operation;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'anesth_perop';
    $spec->key   = 'anesth_perop_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["operation_id"] = "ref notNull class|COperation";
    $props["libelle"]      = "text notNull helped";
    $props["datetime"]     = "dateTime notNull";
    $props["incident"]     = "bool default|0";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();

    $this->_view = "$this->libelle à $this->datetime";
  }

  /**
   * Charge l'intervention
   *
   * @return COperation
   */
  function loadRefOperation(){
    $this->_ref_operation = new COperation();
    return $this->_ref_operation = $this->_ref_operation->getCached($this->operation_id);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    if (!$this->_ref_operation) {
      $this->loadRefOperation();
    }
    return $this->_ref_operation->getPerm($permType);
  }
}
