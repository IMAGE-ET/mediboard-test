<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementsServeurActivitePmsi extends CHPrimXMLDocument {
  static $evenements = array(
    'evenementPMSI'        => "CHPrimXMLEvenementsPmsi",
    'evenementServeurActe' => "CHPrimXMLEvenementsServeurActes",
  );
  
  function __construct($dirschemaname, $schemafilename = null) {
    $this->type = "pmsi";
    
    $version = CAppUI::conf("hprimxml $this->evenement version");
    if ($version == "1.01") {
      parent::__construct($dirschemaname, $schemafilename."101");
    } else if ($version == "1.05") {
      parent::__construct("serveurActivitePmsi", $schemafilename."105");
    }   
  }
  
  function getDateInterv($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    // Obligatoire pour MB
    $debut = $xpath->queryUniqueNode("hprim:debut", $node, false);
    
    return $xpath->queryTextNode("hprim:date", $debut);
  }
  
  function mappingActesCCAM($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $actesCCAM = array();
    foreach ($node->childNodes as $_acteCCAM) {
      $actesCCAM[] = $this->mappingActeCCAM($_acteCCAM);
    }
    mbTrace($actesCCAM, "actesCCAM", true);
    return $actesCCAM;
  }
  
  function mappingActeCCAM($node) {
    $xpath = new CMbXPath($node->ownerDocument, true);
    
    $idEmetteur       = $xpath->queryTextNode("hprim:identifiant/hprim:emetteur", $node);
    $codeActe         = $xpath->queryTextNode("hprim:codeActe", $node);
    $codeActivite     = $xpath->queryTextNode("hprim:codeActivite", $node);
    $codePhase        = $xpath->queryTextNode("hprim:codePhase", $node);
    $date             = $xpath->queryTextNode("hprim:execute/hprim:date", $node);
    $heure            = mbTransformTime($xpath->queryTextNode("hprim:execute/hprim:heure", $node), null , "%H:%M:%S");
    $execute          = "$date $heure";
    $medecinExecutant = $xpath->queryUniqueNode("hprim:executant/hprim:medecins/hprim:medecinExecutant", $node);
    $id400 = new CIdSante400();
    $id400->object_class = "CMediusers";
    $id400->tag = $this->getTagMediuser();
    $id400->id400 = $xpath->queryTextNode("hprim:identification/hprim:code", $medecinExecutant);
    mbTrace($id400, "id400", true);
    $id400->loadMatchingObject();
    $mediuser_id = $id400->object_id;
    $codeAssociationNonPrevue = $xpath->queryTextNode("hprim:codeAssociationNonPrevue", $node);
    
    return array (
      "idEmetteur"   => $idEmetteur,
      "codeActe"     => $codeActe,
      "codeActivite" => $codeActivite,
      "codePhase"    => $codePhase,
      "execute"      => $execute,
      "mediuser_id"  => $mediuser_id,
      "codeAssociationNonPrevue" => $codeAssociationNonPrevue
    );
  }
  
}

?>