<?php 

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron  
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


CAppUI::requireModuleClass("dPinterop", "mbxmldocument");
CAppUI::requireModuleClass("dPinterop", "hprimxmldocument");

if (!class_exists("CHPrimXMLDocument")) {
  return;
}

class CHPrimXMLEvenementsPatients extends CHPrimXMLDocument { 
  function __construct() {    
    global $AppUI, $g;
        
    parent::__construct("evenementPatient", "msgEvenementsPatients105", "sip");

    $evenementsPatients = $this->addElement($this, "evenementsPatients", null, "http://www.hprim.org/hprimXML");
    // Retourne un message d'acquittement par le récepteur
    $this->addAttribute($evenementsPatients, "acquittementAttendu", "oui");
    
    $enteteMessage = $this->addElement($evenementsPatients, "enteteMessage");
    $this->addElement($enteteMessage, "identifiantMessage", "ES{$this->now}");
    $this->addDateTimeElement($enteteMessage, "dateHeureProduction");
    
    $emetteur = $this->addElement($enteteMessage, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, "acteur", "user$AppUI->user_id", "$AppUI->user_first_name $AppUI->user_last_name");
    $this->addAgent($agents, "système", CAppUI::conf('mb_id'), $group->text);
    
    $destinataire = $this->addElement($enteteMessage, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
  }
  
  function generateFromOperation($mbPatient, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $enregistrementPatient = $this->addElement($evenementPatient, "enregistrementPatient");
    $actionConversion = array (
      "create" => "création",
      "store" => "modification",
      "delete" => "suppression"
    );
    $this->addAttribute($enregistrementPatient, "action", $actionConversion[$mbPatient->_ref_last_log->type]);

    // Ajout du patient   
    $this->addPatient($enregistrementPatient, $mbPatient, null, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function generateEvenementsPatients($mbObject, $referent = null, $cip = null) {
    $this->generateFromOperation($mbObject, $referent);
    $doc_valid = $this->schemaValidate();
    $this->saveTempFile();
    $messageEvtPatient = utf8_encode($this->saveXML());
    
    if($cip)
      $this->saveMessageFile(CAppUI::conf('mb_id')."/$cip/evt", "$this->now.xml", $messageEvtPatient);
    else
      $this->saveMessageFile(CAppUI::conf('mb_id')."/evt", "$this->now.xml", $messageEvtPatient);
    
    return $messageEvtPatient;
  }
}
?>