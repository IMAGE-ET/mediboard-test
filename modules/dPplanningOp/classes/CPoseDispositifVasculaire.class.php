<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CPoseDispositifVasculaire extends CMbObject {
  public $pose_dispositif_vasculaire_id;

  public $operation_id;
  public $sejour_id;
  public $date;
  public $lieu;
  public $urgence;
  public $operateur_id;
  public $encadrant_id;
  public $type_materiel;
  public $voie_abord_vasc;

  /** @var CSejour */
  public $_ref_sejour;

  /** @var int Count of signed check lists */
  public $_count_signed;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "pose_dispositif_vasculaire";
    $spec->key   = "pose_dispositif_vasculaire_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["operation_id"]    = "ref class|COperation";
    $props["sejour_id"]       = "ref class|CSejour notNull";
    $props["date"]            = "dateTime notNull";
    $props["lieu"]            = "str";
    $props["urgence"]         = "bool notNull";
    $props["operateur_id"]    = "ref class|CMediusers notNull";
    $props["encadrant_id"]    = "ref class|CMediusers";
    $props["type_materiel"]   = "enum notNull list|cvc|cvc_tunnelise|cvc_dialyse|cvc_bioactif|chambre_implantable|autre";
    $props["voie_abord_vasc"] = "text";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["check_lists"] = "CDailyCheckList object_id";
    $backProps["check_list_categories"] = "CDailyCheckItemCategory target_id";
    return $backProps;
  }

  /**
   * @return CSejour
   */
  function loadRefSejour($cache = true){
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", $cache);
  }

  /**
   * @return int Number of signed check lists
   */
  function countSignedCheckLists(){
    $_count_signed = 0;
    $lists = $this->loadBackRefs("check_lists");
    foreach ($lists as $_list) {
      if ($_list->validator_id) {
        $_count_signed++;
      }
    }

    return $this->_count_signed = $_count_signed;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();

    $this->_view = $this->getFormattedValue("date")." - ".$this->getFormattedValue("type_materiel");

    if ($this->urgence) {
      $this->_view .= " - [URG]";
    }

    if ($this->lieu) {
      $this->_view .= " - $this->lieu";
    }
  }
}
