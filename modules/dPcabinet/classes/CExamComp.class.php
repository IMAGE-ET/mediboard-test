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

class CExamComp extends CMbObject {
  public $exam_id;

  // DB References
  public $consultation_id;

  // DB fields
  public $examen;
  public $realisation;
  public $fait;

  /** @var CConsultation */
  public $_ref_consult;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'exams_comp';
    $spec->key   = 'exam_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    $props["consultation_id"] = "ref notNull class|CConsultation";
    $props["examen"]          = "text helped";
    $props["realisation"]     = "enum notNull list|avant|pendant";
    $props["fait"]            = "num min|0 max|1";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->examen;
  }

  /**
   * @return CConsultation
   */
  function loadRefConsult() {
    return $this->_ref_consult = $this->loadFwdRef("consultation_id", true);
  }

  function getPerm($permType) {
    return $this->loadRefConsult()->getPerm($permType);
  }
}
