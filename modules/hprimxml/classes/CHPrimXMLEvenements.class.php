<?php /* $Id: evenements.class.php 8931 2010-05-12 12:58:21Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 8931 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenements extends CHPrimXMLDocument {  
  static $documentElements = array(
    'evenementsPatients'     => "CHPrimXMLEvenementsPatients",
    'evenementsServeurActes' => "CHPrimXMLEvenementsServeurActivitePmsi",
    'evenementsPMSI'         => "CHPrimXMLEvenementsServeurActivitePmsi",
    'evenementsFraisDivers'  => "CHPrimXMLEvenementsServeurActivitePmsi"
  );
  
  static function getHPrimXMLEvenements() {}
  
  function getDocumentElements() {
    return self::$documentElements;
  }
  
  function generateEnteteMessage($type, $version = true) {
    $evenements = $this->addElement($this, $type, null, "http://www.hprim.org/hprimXML");
    if ($version) {
      $this->addAttribute($evenements, "version", CAppUI::conf("hprimxml $this->evenement version"));
    }
    
    $this->addEnteteMessage($evenements);
  }
  
  function getEnteteEvenementXML($type) {
    $data = array();
    $xpath = new CHPrimXPath($this);   

    $entete = $xpath->queryUniqueNode("/hprim:$type/hprim:enteteMessage");
    
    $data['dateHeureProduction'] = mbDateTime($xpath->queryTextNode("hprim:dateHeureProduction", $entete));
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='".$this->getAttSysteme()."']", $agents, false);
    $this->destinataire = $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);    
    
    return $data;
  }
  
  function getAcquittementEvenementXML() {
    // Message événement patient
    if ($dom_evenement instanceof CHPrimXMLEvenementsPatients) {
      return new CHPrimXMLAcquittementsPatients();
    } 
    // Message serveur activité PMSI
    elseif ($dom_evenement instanceof CHPrimXMLEvenementsServeurActivitePmsi) {
      return new CHPrimXMLAcquittementsServeurActivitePmsi();
    }
  }
}
?>