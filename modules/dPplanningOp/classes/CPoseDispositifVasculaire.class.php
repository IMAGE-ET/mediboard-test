<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CPoseDispositifVasculaire extends CMbObject {
  var $pose_dispositif_vasculaire_id = null;

  var $operation_id     = null;
  var $sejour_id        = null;
  var $date             = null;
  var $lieu             = null;
  var $urgence          = null;
  var $operateur_id     = null;
  var $encadrant_id     = null;
  var $type_materiel    = null;
  var $voie_abord_vasc  = null;

  /**
   * @var CSejour
   */
  var $_ref_sejour      = null;

  /**
   * @var int Count of signed check lists
   */
  var $_count_signed    = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "pose_dispositif_vasculaire";
    $spec->key   = "pose_dispositif_vasculaire_id";
    return $spec;
  }

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
