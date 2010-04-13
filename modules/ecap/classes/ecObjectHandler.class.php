<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license OXPL
 */

class CEcObjectHandler extends CMbObjectHandler {
  static $handled = array ("CFicheAutonomie");

  static function isHandled(CMbObject &$mbObject) {
    return in_array($mbObject->_class_name, self::$handled);
  }
  
  function onBeforeStore(CMbObject &$mbObject) {
  }
  
  function onAfterStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }

    $params = array();
		
		// Login/password
    $source = CExchangeSource::get("ecap_ssr");
		$params["Login"]    = $source->user;
    $params["Password"] = $source->password;
    
		// Identifiant Clinique
    $idex = new CIdSante400;
    $idex->loadLatestFor(CGroups::loadCurrent(), "eCap");
    $params["IdClinique"] = $idex->id400;

    // Identifiant DHE
		$fiche = $mbObject;
		$fiche->loadRefSejour();
		$sejour =& $fiche->_ref_sejour;
		$sejour->loadNumDossier();
    $params["IdDHE"] = $sejour->_num_dossier;
		
		// Identifiant Patient
		$sejour->loadRefPatient();
		$patient =& $sejour->_ref_patient;
    $patient->loadIPP();
    $params["IdPatient"] = $patient->_IPP;
		
		// Valeurs Fiche d'autonomie
    $params["EstSaisie"] = true;
    $params["EstComplete"] = true;

//    mbTrace($params);
    
    $source->setData($params);
    $source->send("SaveFicheMedicaleSaisie");
		if (null == $acquittement = $source->receive()) {
			trigger_error("Couldn't use Exchange source");
      return;
		}
		
		$acq_fiches = simplexml_load_string($acquittement->SaveFicheMedicaleSaisieResult->any);
		$acq_fiche = $acq_fiches->Fiche;
    $acq_ok  = utf8_decode($acq_fiche->attributes()->EstOK);
    $acq_msg = utf8_decode($acq_fiche[0]);

//    mbTrace($acq_fiche, "Acq Fiche");
//    mbTrace($acq_ok, "Acq OK");
//    mbTrace($acq_msg, "Acq Msg");
    
		CAppUI::setMsg("Notification eCap: $acq_msg", $acq_ok == "True" ? UI_MSG_OK : UI_MSG_ERROR);
  }
}
?>