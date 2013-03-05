<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'anesth_perop';
    $spec->key   = 'anesth_perop_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["operation_id"] = "ref notNull class|COperation";
    $specs["libelle"]      = "text notNull helped";
    $specs["datetime"]     = "dateTime notNull";
    $specs["incident"]     = "bool default|0";
    return $specs;
  }

  function updateFormFields(){
    parent::updateFormFields();

    $this->_view = "$this->libelle à $this->datetime";
  }

  /**
   * @return COperation
   */
  function loadRefOperation(){
    $this->_ref_operation = new COperation();
    return $this->_ref_operation = $this->_ref_operation->getCached($this->operation_id);
  }

  function getPerm($permType) {
    if (!$this->_ref_operation) {
      $this->loadRefOperation();
    }
    return $this->_ref_operation->getPerm($permType);
  }
}
