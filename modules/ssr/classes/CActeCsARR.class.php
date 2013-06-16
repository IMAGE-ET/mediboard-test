<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Actes SSR de la nomenclature CsARR
 */
class CActeCsARR extends CActeSSR {
  public $acte_csarr_id;

  // DB Fields
  public $modulateurs;
  public $phases;

  // Derived feilds
  public $_modulateurs = array();
  public $_phases      = array();
  public $_fabrication;

  /** @var CActiviteCsARR */
  public $_ref_activite_csarr;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_csarr';
    $spec->key   = 'acte_csarr_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["code"]        = "str notNull length|7 show|0";
    $props["modulateurs"] = "str maxLength|20";
    $props["phases"]      = "str maxLength|3";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    if ($this->modulateurs) {
      $this->_view .= "-$this->modulateurs";
      $this->_modulateurs = explode("-", $this->modulateurs);
    }

    if ($this->phases) {
      $this->_view .= ".$this->phases";
      $this->_phases = str_split($this->phases);
    }
  }

  /**
   * @see parent::updatePlainFields()
   */
  function updatePlainFields() {
    parent::updatePlainFields();
    $this->modulateurs = implode("-", $this->_modulateurs);
    $this->phases = implode("", $this->_phases     );
  }
  /**
   * Chargement de l'activité associée
   *
   * @return CActiviteCdARR
   */
  function loadRefActiviteCsarr() {
    $activite = CActiviteCsARR::get($this->code);
    $this->_fabrication = strpos($activite->hierarchie, "09.02.02.") === 0;
    $activite->loadRefHierarchie();
    return $this->_ref_activite_csarr = $activite;
  }


  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();
    $this->loadRefActiviteCsARR();
  }
}
