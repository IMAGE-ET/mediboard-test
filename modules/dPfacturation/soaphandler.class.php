<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


/**
 * The CSoapHandler class
 */
class CSoapHandler {
  
  function initClientSOAP ($rooturl, $login, $password) {
    if (preg_match('#\%u#', $rooturl)) 
      $rooturl = str_replace('%u', $login, $rooturl);
    
    if (preg_match('#\%p#', $rooturl)) 
      $rooturl = str_replace('%p', $password, $rooturl);

    if (!$clientSOAP = new CMbSOAPClient($rooturl)) {
      trigger_error("Instanciation du SoapClient impossible.");
    }
    
    return $clientSOAP;
  }
  
  function getEvenementPatientXML($messagePatient) {
    global $m;

    $domEvenement = new CHPrimXMLDocument("evenementPatient", "msgEvenementsPatients105", $m);
    $domEvenement->loadXML(utf8_decode($messagePatient));

    $doc_valid = $domEvenement->schemaValidate();

    $data = array();

    $xpath = new CMbXPath($domEvenement);
    $xpath->registerNamespace( "hprim", "http://www.hprim.org/hprimXML" );

    $data['xpath'] = $xpath;

    $data['acquittement'] = $xpath->queryAttributNode("/hprim:evenementsPatients", null, "acquittementAttendu");

    $query = "/hprim:evenementsPatients/hprim:enteteMessage";

    $entete = $xpath->queryUniqueNode($query);

    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents);
    $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $enregistrementPatient = $xpath->queryUniqueNode("hprim:enregistrementPatient", $evenementPatient);

    $data['action'] = $xpath->getActionEvenement($evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $enregistrementPatient);
    $data['voletMedical'] = $xpath->queryUniqueNode("hprim:voletMedical", $enregistrementPatient);

    $data['idSource'] = $xpath->getIdSource($data['patient']);
    $data['idCible'] = $xpath->getIdCible($data['patient']);

    return $data;
  }

  function getAcquittementEvenementPatient($msgCIP, $erreur) {
    $domAcquittement = new CHPrimXMLAcquittementsPatients();
    // Erreur
    if ($erreur) {
      $domAcquittement->generateEnteteMessageAcquittement("erreur", $msgCIP, $erreur);
    } else {
      $domAcquittement->generateEnteteMessageAcquittement("OK", $msgCIP);
    }
    
    $doc_valid = $domAcquittement->schemaValidate();
    $domAcquittement->saveTempFile();
    $messageAcquittement = utf8_encode($domAcquittement->saveXML());
    
    return $messageAcquittement;
  }
}
?>