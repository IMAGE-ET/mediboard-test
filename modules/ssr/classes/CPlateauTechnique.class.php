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

class CPlateauTechnique extends CMbObject {
  // DB Table key
  public $plateau_id;

  // References
  public $group_id;

  // DB Fields
  public $nom;
  public $repartition;

  // Collections
  public $_ref_equipements;
  public $_ref_techniciens;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'plateau_technique';
    $spec->key   = 'plateau_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref notNull class|CGroups";
    $props["nom"]         = "str notNull";
    $props["repartition"] = "bool default|1";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["equipements"] = "CEquipement plateau_id";
    $backProps["techniciens"] = "CTechnicien plateau_id";
    $backProps["destination_brancardage"]        = "CDestinationBrancardage object_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  function loadRefsEquipements($actif = true) {
    $order = "nom ASC";
    $this->_ref_equipements = $this->loadBackRefs("equipements", $order);
    foreach ($this->_ref_equipements as $_equipement) {
      if ($actif && !$_equipement->actif) {
        unset($this->_ref_equipements[$_equipement->_id]);
        continue;
      }
    }

    return $this->_ref_equipements;
  }

  function loadRefsTechniciens($actif = true) {
    $this->_ref_techniciens = $this->loadBackRefs("techniciens");
    foreach ($this->_ref_techniciens as $_technicien) {
      if ($actif && !$_technicien->actif) {
        unset($this->_ref_techniciens[$_technicien->_id]);
        continue;
      }
      $_technicien->loadRefKine();
    }

    $sorter = CMbArray::pluck($this->_ref_techniciens, "_view");
    array_multisort($sorter, SORT_ASC, $this->_ref_techniciens);
    return $this->_ref_techniciens;
  }
}
