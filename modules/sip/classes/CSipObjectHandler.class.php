<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSipObjectHandler extends CEAIObjectHandler {
  static $handled = array ("CPatient", "CCorrespondantPatient", "CIdSante400");
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
  
  function onAfterStore(CMbObject $mbObject) {
    if (!parent::onAfterStore($mbObject)) {
      return;
    }
    
    // Si pas de tag patient
    if (!CAppUI::conf("dPpatients CPatient tag_ipp")) {
      throw new CMbException("no_tag_defined");
    }

    // Si serveur et pas d'IPP sur le patient
    if ((isset($mbObject->_no_ipp) && ($mbObject->_no_ipp == 1)) &&
        CAppUI::conf('sip server')) {
      return;
    }
    
    $this->sendFormatAction("onAfterStore", $mbObject);
  }

  function onBeforeMerge(CMbObject $mbObject) {
    if (!parent::onBeforeMerge($mbObject)) {
      return;
    }
    
    // Si pas en mode alternatif
    if (!CAppUI::conf("alternative_mode")) {
      throw new CMbException("no_alternative_mode");
    }
    
    $patient = $mbObject;

    $patient_elimine = new CPatient();
    $patient_elimine->load(reset($mbObject->_merging));

    // Si Client
    if (!CAppUI::conf('sip server')) {
      $mbObject->_fusion = array();
      foreach (CGroups::loadGroups() as $_group) {
        if ($mbObject->_eai_initiateur_group_id == $_group->_id) {
          continue;
        }
        $patient->_IPP = null;
        $patient->loadIPP($_group->_id);
        $patient1_ipp = $patient->_IPP;

        $patient_elimine->_IPP = null;
        $patient_elimine->loadIPP($_group->_id);
        $patient2_ipp = $patient_elimine->_IPP;

        // Passage en trash des IPP des patients
        $tap_IPP = CPatient::getTagIPP($_group->_id);
        
        $id400Patient               = new CIdSante400();
        $id400Patient->tag          = $tap_IPP;
        $id400Patient->object_class = "CPatient";
        $id400Patient->object_id    = $patient->_id;
        $id400sPatient = $id400Patient->loadMatchingList();
        
        $id400PatientElimine               = new CIdSante400();
        $id400PatientElimine->tag          = $tap_IPP;
        $id400PatientElimine->object_class = "CPatient";
        $id400PatientElimine->object_id    = $patient_elimine->_id;
        $id400sPatientElimine = $id400PatientElimine->loadMatchingList();

        $id400s = array_merge($id400sPatient, $id400sPatientElimine);
        if (count($id400s) > 1) {
          foreach ($id400s as $_id_400) {
            // On continue pour ne pas mettre en trash l'IPP du patient que l'on garde
            if ($_id_400->id400 == $patient1_ipp) {
              continue;
            }

            $patient_elimine->trashIPP($_id_400);
          }
        }
        
        if (!$patient1_ipp && !$patient2_ipp) {
          continue;  
        }
        
        $mbObject->_fusion[$_group->_id] = array (
          "patientElimine" => $patient_elimine,
          "patient1_ipp"   => $patient1_ipp,
          "patient2_ipp"   => $patient2_ipp,
        );
       
      }        
    }

    $this->sendFormatAction("onBeforeMerge", $mbObject);
  }
  
  function onAfterMerge(CMbObject $mbObject) {
    if (!parent::onAfterMerge($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterMerge", $mbObject);
  }
  
  function onAfterDelete(CMbObject $mbObject) {
    if (!parent::onAfterDelete($mbObject)) {
      return;
    }
    
    $this->sendFormatAction("onAfterDelete", $mbObject);
  }
}
?>