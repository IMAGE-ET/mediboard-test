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
    'evenementsPatients'           => "CHPrimXMLEvenementsPatients",
    'evenementsServeurActes'       => "CHPrimXMLEvenementsServeurActivitePmsi",
    'evenementsPMSI'               => "CHPrimXMLEvenementsServeurActivitePmsi",
    'evenementsFraisDivers'        => "CHPrimXMLEvenementsServeurActivitePmsi",
    'evenementServeurIntervention' => "CHPrimXMLEvenementsServeurActivitePmsi",
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
    
    $data['dateHeureProduction'] = CMbDT::dateTime($xpath->queryTextNode("hprim:dateHeureProduction", $entete));
    $data['identifiantMessage'] = $xpath->queryTextNode("hprim:identifiantMessage", $entete);
    $agents = $xpath->queryUniqueNode("hprim:emetteur/hprim:agents", $entete);
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='".$this->getAttSysteme()."']", $agents, false);
    $this->destinataire = $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);    
    
    return $data;
  }
  
  function getActionEvenement($query, $node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->queryAttributNode($query, $node, "action");    
  }
  
  function isActionValide($action, $dom_acq) {
    $acq = null;
    $echange_hprim = $this->_ref_echange_hprim;

    if (!$action || array_key_exists($action, $this->actions)) {
      return $acq;
    }
    
    $acq       = $dom_acq->generateAcquittements("erreur", "E008");
    $doc_valid = $dom_acq->schemaValidate();
    
    $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
    $echange_hprim->_acquittement       = $acq;
    $echange_hprim->statut_acquittement = "erreur";
    $echange_hprim->store();
    
    return $acq;
  }
  
  function getDate($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->queryTextNode("hprim:date", $node);
  }
  
  function getHeure($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $xpath->queryTextNode("hprim:heure", $node);
  }
  
  function getDateHeure($node) {
    return $this->getDate($node)." ".$this->getHeure($node);
  }
}
?>