<?php
/**
 * $Id: CLibelleOp.class.php 5278 2014-01-29 10:44:13Z nicolasld $
 *
 * @package    Mediboard
 * @subpackage dPplanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision: 5278 $
 */

/**
 * Classe gérant les libellés opératoires
 */
class CLibelleOp extends CMbObject {
  // DB Table key
  public $libelleop_id;

  // DB fields
  public $group_id;
  public $statut;
  public $nom;
  public $date_debut;
  public $date_fin;
  public $services;
  public $mots_cles;
  public $numero;
  public $version;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = 'libelleop';
    $spec->key    = 'libelleop_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["group_id"]    = "ref notNull class|CGroups autocomplete|text";
    $props["statut"]      = "enum list|valide|no_valide|indefini";
    $props["nom"]         = "str notNull";
    $props["date_debut"]  = "dateTime";
    $props["date_fin"]    = "dateTime";
    $props["services"]    = "str";
    $props["mots_cles"]   = "str";
    $props["numero"]      = "num notNull";
    $props["version"]     = "num default|1";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["liaison_interv"] = "CLiaisonLibelleInterv libelleop_id";
    return $backProps;
  }

  /**
   * updateFormFields
   *
   * @return void
   **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->nom;
  }

  /**
   * @see parent::check()
   */
  function check(){
    return parent::check();
  }

}
