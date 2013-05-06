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

class CActeCdARR extends CActeSSR {
  public $acte_cdarr_id;
    
  /** @var CActiviteCdARR */
  public $_ref_activite_cdarr;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'acte_cdarr';
    $spec->key   = 'acte_cdarr_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["code"] = "str notNull length|4 show|0";
    return $props;
  }

  function loadRefActiviteCdARR() {
    $activite = CActiviteCdARR::get($this->code);
    $activite->loadRefTypeActivite();
    return $this->_ref_activite_cdarr = $activite;
  }
  
  function loadView(){
    parent::loadView();
    $this->loadRefActiviteCdARR();
  }
}
