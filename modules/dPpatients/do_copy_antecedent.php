<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sébastien Fillonneau
*/

class CDoCopyAntecedent extends CDoObjectAddEdit {
  function CDoCopyAntecedent() {
    $this->CDoObjectAddEdit("CAntecedent", "antecedent_id");
  }  
  
  function doBind() {
    parent::doBind();
    
    // recuperation du sejour_id
    $_sejour_id = CValue::post("_sejour_id"  , null);

    // si pas de sejour_id, redirection
    if (!$_sejour_id){
       $this->doRedirect();
    }
    
    // Creation du nouvel antecedent
    unset($_POST["antecedent_id"]);
    $this->_obj = $this->_objBefore;
    $this->_obj->_id = null;
    $this->_obj->antecedent_id = null;
    
    // Calcul de la valeur de l'id du dossier_medical du sejour
    $this->_obj->dossier_medical_id = CDossierMedical::dossierMedicalId($_sejour_id,"CSejour");
  }
}
$do = new CDoCopyAntecedent;
$do->doIt();
?>