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
 * Technicien de SSR, association entre un plateau technique et un utilisateur
 */
class CTechnicien extends CMbObject {
  // DB Table key
  public $technicien_id;

  // References fields
  public $plateau_id;
  public $kine_id;

  // DB Fields
  public $actif;

  // Form fields
  public $_transfer_id;
  public $_count_sejours_date;

  // References
  public $_ref_kine;
  public $_ref_plateau;


  // Derived references
  public $_ref_conge_date;
  public $_ref_sejours_date;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'technicien';
    $spec->key   = 'technicien_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["plateau_id"] = "ref notNull class|CPlateauTechnique";
    $props["kine_id"]    = "ref notNull class|CMediusers";
    $props["actif"]      = "bool notNull default|1";

    $props["_transfer_id"]        = "ref class|CTechnicien";
    $props["_count_sejours_date"] = "num";

    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["bilan_ssr"] = "CBilanSSR technicien_id";
    return $backProps;
  }

  function store() {
    // Transfert de séjours vers un autre technicien
    if ($this->_transfer_id) {
      foreach ($this->loadRefsSejours(CMbDT::date()) as $_sejour) {
        $bilan = $_sejour->loadRefBilanSSR();
        $bilan->technicien_id = $this->_transfer_id;
        if ($msg = $bilan->store()) {
          return $msg;
        }
      }
    }

    return parent::store();
  }

  function updateView() {
    $parts = array();
    if ($this->_ref_kine && $this->_ref_kine->_id) {
      $parts[] = $this->_ref_kine->_view;
    }

    if ($this->_ref_plateau && $this->_ref_plateau->_id) {
      $parts[] = $this->_ref_plateau->_view;
    }

    $this->_view = implode(" &ndash; ",$parts);
  }

  function loadRefPlateau() {
    $this->_ref_plateau = $this->loadFwdRef("plateau_id", true);
    $this->updateView();
    return $this->_ref_plateau;
  }	


  function loadRefKine() {
    $this->_ref_kine = $this->loadFwdRef("kine_id", true);
    $this->_ref_kine->loadRefFunction();
    $this->updateView();
    return $this->_ref_kine;
  }

  function loadRefCongeDate($date) {
    $this->_ref_conge_date = new CPlageConge;
    $this->_ref_conge_date->loadFor($this->kine_id, $date);
    return $this->_ref_conge_date;
  }

  function countSejoursDate($date) {
    $group = CGroups::loadCurrent();
    $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group->_id'";
    $where["annule"] = "= '0'";
    $where["bilan_ssr.technicien_id"] = "= '$this->_id'";
    return $this->_count_sejours_date = CSejour::countForDate($date, $where, $leftjoin);
  }

  function loadRefsSejours($date) {
    $group = CGroups::loadCurrent();
    $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group->_id'";
    $where["annule"] = "= '0'";
    $where["bilan_ssr.technicien_id"] = "= '$this->_id'";
    return $this->_ref_sejours_date = CSejour::loadListForDate($date, $where, null, null, null, $leftjoin);
  }
}
