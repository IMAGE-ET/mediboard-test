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
    $props["code"] = "str notNull length|7 show|0";
    return $props;
  }

  /**
   * Chargement de l'activité associée
   *
   * @return CActiviteCdARR
   */
  function loadRefActiviteCsarr() {
    $activite = CActiviteCsARR::get($this->code);
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
