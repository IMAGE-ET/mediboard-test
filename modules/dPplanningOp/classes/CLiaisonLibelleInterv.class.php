<?php
/**
 * $Id: CLiaisonLibelleInterv.class.php 4582 2013-10-16 12:34:34Z ahebras $
 *
 * @package    Mediboard
 * @subpackage dPplanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision: 4582 $
 */

/**
 * Liaison entre les intervention et les libellés
 */
class CLiaisonLibelleInterv extends CMbObject {

  // DB Table key
  public $liaison_libelle_id;

  // DB Fields
  public $libelleop_id;
  public $operation_id;
  public $numero;

  // Object References
  /** @var  COperation $_ref_operation*/
  public $_ref_operation;
  /** @var  CLibelleOp $_ref_libelle*/
  public $_ref_libelle;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'liaison_libelle';
    $spec->key   = 'liaison_libelle_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["libelleop_id"]  = "ref notNull class|CLibelleOp autocomplete|nom dependsOn|group_id";
    $props["operation_id"]  = "ref notNull class|COperation";
    $props["numero"]        = "num min|1 default|1";
    return $props;
  }

  /**
   * Chargement de l'intervention
   *
   * @return COperation
   */
  function loadRefOperation() {
    return $this->_ref_operation = $this->loadFwdRef("operation_id", true);
  }

  /**
   * Chargement du libellé
   *
   * @return CLibelleOp
   */
  function loadRefLibelle() {
    return $this->_ref_libelle = $this->loadFwdRef("libelleop_id", true);
  }
}