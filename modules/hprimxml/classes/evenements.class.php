<?php /* $Id: evenements.class.php 8931 2010-05-12 12:58:21Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 8931 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenements extends CHPrimXMLDocument {
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
    $systeme = $xpath->queryUniqueNode("hprim:agent[@categorie='système']", $agents, false);
    $this->destinataire = $data['idClient'] = $xpath->queryTextNode("hprim:code", $systeme);
    $data['libelleClient'] = $xpath->queryTextNode("hprim:libelle", $systeme);    
    
    return $data;
  }
  
}
?>