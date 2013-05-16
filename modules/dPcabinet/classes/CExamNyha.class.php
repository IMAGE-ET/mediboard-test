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

class CExamNyha extends CMbObject {
  // DB Table key
  public $examnyha_id;

  // DB References
  public $consultation_id;

  // DB fields
  public $q1;
  public $q2a;
  public $q2b;
  public $q3a;
  public $q3b;
  public $hesitation;

  /** @var CConsultation */
  public $_ref_consult;

  // Form fields
  public $_classeNyha;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'examnyha';
    $spec->key   = 'examnyha_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["consultation_id"] = "ref notNull class|CConsultation";
    $props["q1"]              = "bool default|none";
    $props["q2a"]             = "bool";
    $props["q2b"]             = "bool";
    $props["q3a"]             = "bool";
    $props["q3b"]             = "bool";
    $props["hesitation"]      = "bool notNull";

    $props["_classeNyha"] = "";
    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->_classeNyha = "";

    if ($this->q1 == 1) {
      if ($this->q2a !== null && $this->q2a == 0) {
        $this->_classeNyha = "Classe III";
      }
      if ($this->q2a == 1 && $this->q2b !== null && $this->q2b == 1) {
        $this->_classeNyha = "Classe I";
      }
      if ($this->q2a == 1 && $this->q2b !== null && $this->q2b == 0) {
        $this->_classeNyha = "Classe II";
      }
    }
    elseif ($this->q1 == 0) {
      if ($this->q3a !== null && $this->q3a == 0) {
        $this->_classeNyha = "Classe III";
      }
      if ($this->q3a == 1 && $this->q3b !== null && $this->q3b == 1) {
        $this->_classeNyha = "Classe III";
      }
      if ($this->q3a == 1 && $this->q3b !== null && $this->q3b == 0) {
        $this->_classeNyha = "Classe IV";
      }
    }

    $this->_view = "Classification NYHA : $this->_classeNyha"; 
  }

  function loadRefsFwd() {
    $this->_ref_consult = new CConsultation;
    $this->_ref_consult->load($this->consultation_id);
  }

  function getPerm($permType) {
    if (!$this->_ref_consult) {
      $this->loadRefsFwd();
    }

    return $this->_ref_consult->getPerm($permType);
  }
}
