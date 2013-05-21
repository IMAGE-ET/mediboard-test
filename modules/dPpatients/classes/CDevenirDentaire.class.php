<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Class CDevenirDentaire
 */
class CDevenirDentaire extends CMbObject {
  // DB Table key
  public $devenir_dentaire_id;

  // DB Fields
  public $patient_id;
  public $etudiant_id;
  public $description;
  
  // Back references
  public $_ref_actes_dentaires;
  public $_ref_patient;
  public $_ref_etudiant;
  
  // Form fields
  public $_total_ICR           = 0;
  public $_max_ICR             = 0;
  public $_count_ref_actes_dentaires = 0;

  // Distant fields
  public $_nb_actes_planifies  = 0;
  public $_nb_actes_realises   = 0;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'devenir_dentaire';
    $spec->key   = 'devenir_dentaire_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["patient_id"]  = "ref notNull class|CPatient";
    $specs["etudiant_id"] = "ref class|CMediusers";
    $specs["description"] = "text notNull";
    return $specs;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["actes_dentaires"] = "CActeDentaire devenir_dentaire_id";
    return $backProps;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->description; 
  }

  /**
   * Charge les actes dentaires
   *
   * @return CActeDentaire[]
   */
  function loadRefsActesDentaires() {
    return $this->_ref_actes_dentaires = $this->loadBackRefs("actes_dentaires", "rank");
  }

  /**
   * Compte les actes dentaires
   *
   * @return int
   */
  function countRefsActesDentaires() {
    return $this->_count_ref_actes_dentaires = $this->countBackRefs("actes_dentaires");
  }

  /**
   * Charge l'étudiant
   *
   * @return CMediusers
   */
  function loadRefEtudiant() {
    return $this->_ref_etudiant = $this->loadFwdRef("etudiant_id");
  }

  /**
   * Charge le patient
   *
   * @return CPatient
   */
  function loadRefPatient() {
    return $this->_ref_patient = $this->loadFwdRef("patient_id");
  }
}
