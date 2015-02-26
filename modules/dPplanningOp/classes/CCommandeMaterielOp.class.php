<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

class CCommandeMaterielOp extends CMbObject {
  // DB Table key
  public $commande_materiel_id;

  // DB Fields
  public $operation_id;
  public $etat;
  public $date;
  public $commentaire;

  /** @var COperation */
  public $_ref_operation;
  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = 'operation_commande';
    $spec->key    = 'commande_materiel_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["operation_id"]= "ref notNull class|COperation seekable";
    $props["date"]        = "date";
    $props["etat"]        = "enum notNull list|a_commander|modify|commandee|recue|a_annuler|annulee default|a_commander";
    $props["commentaire"] = "text";
    return $props;
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("operation_id");

    if ($this->_id && $this->etat != "a_commander") {
      if (CMediusers::get()->_id == $this->loadRefOperation()->chir_id) {
        $this->etat = "modify";
      }
    }

    // Standard storage
    if ($msg = parent::store()) {
      return $msg;
    }

  }

  /**
   * @see parent::updateFormFields()
   */
  function loadView() {
    parent::loadView();
    $this->_view = "Commande du ". CMbDT::format($this->date, CAppUI::conf("date"));
    $this->_view .= " pour ". $this->loadRefOperation()->loadRefPatient();
  }

  function cancelledOp() {
    $this->etat = 'a_annuler';
    if ($msg = $this->store()) {
      return $msg;
    }
  }

  /**
   * Chargement de l'intervention
   *
   * @param bool $cache Utilisation du cache
   *
   * @return COperation
   */
  function loadRefOperation($cache = true) {
    return $this->_ref_operation = $this->loadFwdRef("operation_id", $cache);
  }

}