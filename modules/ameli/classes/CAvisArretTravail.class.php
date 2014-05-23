<?php

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */
 
/**
 * Description
 */
class CAvisArretTravail extends CMbObject {

  public $motif_id;

  public $libelle_motif;

  public $type;

  public $accident_tiers;

  public $date_accident;

  public $debut;

  public $fin;

  public $consult_id;

  public $patient_id;

  public $_duree;

  public $_unite_duree;
  /** @var CMotifArretTravail  */
  public $_ref_motif;

  /** @var CConsultation */
  public $_ref_consult;

  /** @var CPatient */
  public $_ref_patient;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = 'avis_arret_travail';
    $spec->key    = 'avis_arret_travail_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['motif_id']        = 'str';
    $props['libelle_motif']   = 'str notNull';
    $props['type']            = 'enum list|initial|prolongation notNull default|initial';
    $props['accident_tiers']  = 'bool default|0';
    $props['date_accident']   = 'date';
    $props['debut']           = 'date notNull';
    $props['fin']             = 'date notNull';
    $props['consult_id']      = 'ref class|CConsultation';
    $props['patient_id']      = 'ref class|CPatient';

    return $props;
  }

  /**
   * Load the motif
   *
   * @return void
   */
  public function loadRefMotif() {
    $this->_ref_motif = CMotifArretTravail::searchByCode($this->motif_id);
  }

  /**
   * Load the consultation
   *
   * @return void
   */
  public function loadRefConsult() {
    $this->_ref_consult = CConsultation::loadFromGuid("CConsultation-$this->consult_id");
  }

  /**
   * @see parent::updateFormFields
   */
  public function updateFormFields() {
    parent::updateFormFields();

    if ($this->debut && $this->fin) {
      $this->_duree = CMbDT::daysRelative($this->debut, $this->fin);
    }
  }
}
