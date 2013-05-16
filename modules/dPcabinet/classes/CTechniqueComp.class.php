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

class CTechniqueComp extends CMbObject {
  // DB Table key
  public $technique_id;

  // DB References
  public $consultation_anesth_id;

  // DB fields
  public $technique;

  /** @var CConsultAnesth */
  public $_ref_consult_anesth;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'techniques_anesth';
    $spec->key   = 'technique_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["consultation_anesth_id"] = "ref notNull class|CConsultAnesth";
    $props["technique"]              = "text helped";
    return $props;
  }

  function loadRefsFwd() {
    $this->_ref_consult_anesth = new CConsultAnesth;
    $this->_ref_consult_anesth->load($this->consultation_anesth_id);
  }

  function getPerm($permType) {
    if (!$this->_ref_consult_anesth) {
      $this->loadRefsFwd();
    }
    return $this->_ref_consult_anesth->getPerm($permType);
  }
}
