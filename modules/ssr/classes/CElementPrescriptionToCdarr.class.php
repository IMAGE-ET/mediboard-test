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
 * Classe d'association entre les élément de prescription et les code CdARR
 */
class CElementPrescriptionToCdarr extends CElementPrescriptionToReeducation {
  // DB Table key
  public $element_prescription_to_cdarr_id;
    
  public $_ref_activite_cdarr;
  public $_count_cdarr_by_type;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'element_prescription_to_cdarr';
    $spec->key   = 'element_prescription_to_cdarr_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["code"]                    = "str notNull length|4";
    return $props;
  }

  /**
   * @see parent::check()
   */
  function check() {
    // Verification du code Cdarr saisi
    $code_cdarr = CActiviteCdARR::get($this->code);
    if (!$code_cdarr->code) {
      return "Ce code n'est pas un code CdARR valide";
    }
    return parent::check();
  }

  /**
   * Charge l'activité CdARR associée
   *
   * @return CActiviteCdARR
   */
  function loadRefActiviteCdarr() {
    $activite = CActiviteCdARR::get($this->code);
    $activite->loadRefTypeActivite();
    return $this->_ref_activite_cdarr = $activite;
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->loadRefActiviteCdarr();
  }
}
