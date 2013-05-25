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
 * Gestes (activités) complémentaires CsARR
 */
class CGesteComplementaireCsARR extends CCsARRObject {

  public $code_source;
  public $code_cible;

  public $_ref_code_source;
  public $_ref_code_cible;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'geste_complementaire';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code_source"] = "str notNull length|7";
    $props["code_cible" ] = "str notNull length|7";

    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->code_source => $this->code_cible";
  }

  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();
    $this->loadRefCodeSource();
    $this->loadRefCodeCible();
  }

  /**
   * Charge le code source de l'association
   *
   * @return CActiviteCdARR
   */
  function loadRefCodeSource() {
    return $this->_ref_code_source = CActiviteCdARR::get($this->code_source);
  }

  /**
   * Charge le code cible de l'association
   *
   * @return CActiviteCdARR
   */
  function loadRefCodeCible() {
    return $this->_ref_code_cible = CActiviteCdARR::get($this->code_cible);
  }

}
