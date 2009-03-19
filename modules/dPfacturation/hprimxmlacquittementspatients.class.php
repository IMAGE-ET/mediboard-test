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

class CHPrimXMLAcquittementsPatients extends CHPrimXMLDocument { 
  function __construct() {        
    parent::__construct("evenementPatient", "msgAcquittementsPatients105", "sip");
  }
  
  function generateEnteteMessageAcquittement($statut, $msg, $erreur = null) {
    global $AppUI, $g, $m;
    
    $acquittementsPatients = $this->addElement($this, "acquittementsPatients", null, "http://www.hprim.org/hprimXML");
        
    $enteteMessageAcquittement = $this->addElement($acquittementsPatients, "enteteMessageAcquittement");
    $this->addAttribute($enteteMessageAcquittement, "statut", $statut);
    
    $this->addElement($enteteMessageAcquittement, "identifiantMessage", "ES{$this->now}");
    $this->addDateTimeElement($enteteMessageAcquittement, "dateHeureProduction");
    
    $emetteur = $this->addElement($enteteMessageAcquittement, "emetteur");
    $agents = $this->addElement($emetteur, "agents");
    $this->addAgent($agents, "application", "MediBoard", "Gestion des Etablissements de Santé");
    $group = CGroups::loadCurrent();
    $group->loadLastId400();
    $this->addAgent($agents, "système", CAppUI::conf('mb_id'), $group->text);
    
    $destinataire = $this->addElement($enteteMessageAcquittement, "destinataire");
    $agents = $this->addElement($destinataire, "agents");
    $this->addAgent($agents, "application", $msg['codeAgent'], $msg['libelleAgent']);  
    
    $identifiantMessageAcquitte = $this->addElement($enteteMessageAcquittement, "identifiantMessageAcquitte", $msg['identifiantMessage']);
    
    $this->addObservation($enteteMessageAcquittement, $statut, "INF001");
  }
  
  function addObservation($elParent, $statut, $code) {
    $observation = $this->addElement($elParent, "observation");
    $code = $this->addElement($observation, "code", $code);

    if ($statut == "OK") 
      $commentaire = $this->addElement($observation, "commentaire", "Enregistrement du patient IPP terminee");  
    
    if ($statut == "erreur")
      $commentaire = $this->addElement($observation, "commentaire", $erreur); 
  }
  
  function generateAcquittementsPatients($statut, $msgCIP, $erreur = null, $cip = null) {
    $this->generateEnteteMessageAcquittement($statut, $msgCIP, $erreur = null);
    $doc_valid = $this->schemaValidate();
    $this->saveTempFile();
    $messageAcquittementPatient = utf8_encode($this->saveXML());
    
    if($cip)
      $this->saveMessageFile(CAppUI::conf('mb_id')."/$cip/acq", "$this->now.xml", $messageAcquittementPatient);
    else
      $this->saveMessageFile(CAppUI::conf('mb_id')."/acq", "$this->now.xml", $messageAcquittementPatient);
    
    return $messageAcquittementPatient;
  }
}

?>