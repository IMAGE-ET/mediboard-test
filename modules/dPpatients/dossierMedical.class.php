<?php /* $Id: patients.class.php 2242 2007-07-11 10:21:19Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2242 $
* @author Romain Ollivier
*/

/**
 * Dossier Médical liés aux notions d'antécédents, traitements, addictions et diagnostics
 */
class CDossierMedical extends CMbMetaObject {
  // DB Fields
  var $listCim10    = null;
  
  // Form Fields
  var $_codes_cim10 = null;

  // Back references
  var $_ref_antecedents = null;
  var $_ref_traitements = null;
  var $_ref_addictions  = null;
  
  // Derived back references
  var $_ref_types_addiction  = null;
  
	function CDossierMedical() {
		$this->CMbObject("dossier_medical", "dossier_medical_id");    
    $this->loadRefModule(basename(dirname(__FILE__)));
 	}
  
  
  function getSpecs() {
    $specs = parent::getSpecs();

    $specs["listCim10"]         = "text";
    
    return $specs;
  }  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["antecedents"] = "CAntecedent dossier_medical_id";
    $backRefs["addictions" ] = "CAddiction dossier_medical_id";
    $backRefs["traitements"] = "CTraitement dossier_medical_id";
    return $backRefs;
  }

  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsAntecedents();
    $this->loadRefsTraitements();
    $this->loadRefsAddictions();
  }
  
  function loadRefObject(){  
    $this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id);
  }
 
  
  
  
  function updateFormFields() {
    parent::updateFormFields();
    
    // Codes CIM10
    $this->_codes_cim10 = array();
    $arrayCodes = array();
    if ($this->listCim10)
      $arrayCodes = explode("|", $this->listCim10);
    foreach ($arrayCodes as $value) {
      $this->_codes_cim10[] = new CCodeCIM10($value, 1);
    }
  }  
    
  function loadRefsAntecedents() {
    $order = "type ASC";
    if (null == $antecedents = $this->loadBackRefs("antecedents", $order)) {
      return;
    }

    // Classements des antécédents
    foreach ($antecedents as $_antecedent) {
      $this->_ref_antecedents[$_antecedent->type][$_antecedent->_id] = $_antecedent;
    }
  }

  function loadRefsTraitements() {
    $order = "fin DESC, debut DESC";
    $this->_ref_traitements = $this->loadBackRefs("traitements", $order);
  }
  
  function loadRefsAddictions() {
    global $dPconfig;
    if (!$dPconfig["dPcabinet"]["addictions"]) {
      return;
    }
     
    $order = "type ASC";
    if (null == $this->_ref_addictions = $this->loadBackRefs("addictions", $order)) {
      return;
    }

    // Classement des addictions
    $this->_ref_types_addiction = array();
    foreach ($this->_ref_addictions as $_addiction) {
      $this->_ref_types_addiction[$_addiction->type][$_addiction->_id] = $_addiction;
    }
  }
  
  function dossierMedicalId($object_id, $object_class) {
    $dossier = new CDossierMedical();
    $dossier->object_id    = $object_id;
    $dossier->object_class = $object_class;
    $dossier->loadMatchingObject();
    if(!$dossier->_id) {
      $dossier->store();
    }
    return $dossier->_id;
  }

  function fillTemplate(&$template) {
    global $AppUI;
    
    // Antécédents
    $this->loadRefsAntecedents();
    if(is_array($this->_ref_antecedents)){
      // Réécritude des antécédents
      $sAntecedents = null;
      foreach($this->_ref_antecedents as $keyAnt=>$currTypeAnt){
        if($currTypeAnt){
          if($sAntecedents){$sAntecedents.="<br />";}
          $sAntecedents .= $AppUI->_("CAntecedent.type.".$keyAnt)."\n";
          foreach($currTypeAnt as $currAnt){
            $sAntecedents .= " &bull; ";
            if($currAnt->date){
              $sAntecedents .= substr($currAnt->date, 8, 2) ."/";
              $sAntecedents .= substr($currAnt->date, 5, 2) ."/";
              $sAntecedents .= substr($currAnt->date, 0, 4) ." : ";
            }
            $sAntecedents .= $currAnt->rques;
          }
        }
      }
      $template->addProperty("Patient - antécédents", $sAntecedents);
    }else{
      $template->addProperty("Patient - antécédents");
    }
    

    // Traitements
    $this->loadRefsTraitements();
    if($this->_ref_traitements){
      $sTrmt = null;
      foreach($this->_ref_traitements as $curr_trmt){
        if($sTrmt){$sTrmt.=" &bull; ";}
        if ($curr_trmt->fin){
          $sTrmt .= "Du ";
          $sTrmt .= substr($curr_trmt->debut, 8, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 5, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 0, 4) ." au ";
          $sTrmt .= substr($curr_trmt->fin, 8, 2) ."/";
          $sTrmt .= substr($curr_trmt->fin, 5, 2) ."/";
          $sTrmt .= substr($curr_trmt->fin, 0, 4) ." : ";
        }elseif($curr_trmt->debut){
          $sTrmt .= "Depuis le ";
          $sTrmt .= substr($curr_trmt->debut, 8, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 5, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 0, 4) ." : ";
        }
        $sTrmt .= $curr_trmt->traitement;
      }
      $template->addProperty("Patient - traitements", $sTrmt);
    }else{
      $template->addProperty("Patient - traitements");
    }
    
    // Addictions
    // @todo: Template pour les addictions
    
    // Codes CIM10
    $aCim10 = array();
    if ($this->_codes_cim10){
      foreach ($this->_codes_cim10 as $curr_code){
        $aCim10[] = "$curr_code->code : $curr_code->libelle";
      }
    }
    
    $template->addProperty("Patient - diagnostics", join("&bull;", $aCim10));
  }
}

?>