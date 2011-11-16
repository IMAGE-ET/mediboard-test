<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDevenirDentaire extends CMbObject {
  // DB Table key
  var $devenir_dentaire_id  = null;

  // DB Fields
  var $patient_id           = null;
  var $etudiant_id          = null;
  var $description          = null;
  
  // Back references
  var $_ref_actes_dentaires = null;
  var $_ref_patient         = null;
  var $_ref_etudiant        = null;
  
  // Form fields
  var $_total_ICR           = 0;
  var $_count_ref_actes_dentaires = 0;
  
  // Distant fields
  var $_nb_actes_planifies  = 0;
  var $_nb_actes_realises   = 0;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'devenir_dentaire';
    $spec->key   = 'devenir_dentaire_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["patient_id"]  = "ref notNull class|CPatient";
    $specs["etudiant_id"] = "ref class|CMediusers";
    $specs["description"] = "text notNull";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["actes_dentaires"] = "CActeDentaire devenir_dentaire_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->description; 
  }
  
  function loadRefsActesDentaires() {
    return $this->_ref_actes_dentaires = $this->loadBackRefs("actes_dentaires", "rank");
  }
  
  function countRefsActesDentaires() {
    return $this->_count_ref_actes_dentaires = $this->countBackRefs("actes_dentaires");
  }
  
  function loadRefEtudiant() {
    return $this->_ref_etudiant = $this->loadFwdRef("etudiant_id");
  }
  
  function loadRefPatient() {
    return $this->_ref_patient = $this->loadFwdRef("patient_id");
  }
}
