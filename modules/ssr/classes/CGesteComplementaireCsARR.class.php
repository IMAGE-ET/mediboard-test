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

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'geste_complementaire';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    // DB Fields
    $props["code_source"] = "str notNull length|7";
    $props["code_cible" ] = "str notNull length|7";

    return $props;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->code_source => $this->code_cible";
  }

  function loadRefCodeSource() {
    return $this->_ref_code_source = CActiviteCdARR::get($this->code_source);
  }

  function loadRefCodeCible() {
    return $this->_ref_code_cible = CActiviteCdARR::get($this->code_cible);
  }

  function loadView(){
    parent::loadView();
    $this->loadRefCodeSource();
    $this->loadRefCodeCible();
  }
}
