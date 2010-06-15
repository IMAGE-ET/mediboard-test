<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenements");

class CHPrimXMLEvenementsServeurActivitePmsi extends CHPrimXMLEvenements {
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
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    // Obligatoire pour MB
    $debut = $xpath->queryUniqueNode("hprim:debut", $node, false);
    
    return $xpath->queryTextNode("hprim:date", $debut);
  }
  
	function mappingServeurActes($data) {
		// Mapping patient
		$patient = $this->mappingPatient($data);
		
		// Mapping actes CCAM
		$actesCCAM = $this->mappingActesCCAM($data);
		
		return array (
		  "patient"   => $patient,
		  "actesCCAM" => $actesCCAM
		);  
	}
	
	function mappingPatient($data) {
		$node = $data['patient'];
		$xpath = new CHPrimXPath($node->ownerDocument);
		
		$personnePhysique = $xpath->queryUniqueNode("hprim:personnePhysique", $node);
    $prenoms = $xpath->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $elementDateNaissance = $xpath->queryUniqueNode("hprim:dateNaissance", $personnePhysique);
		
		return array (
      "idSourcePatient" => $data['idSourcePatient'],
      "idCiblePatient"  => $data['idCiblePatient'],
      "nom"             => $xpath->queryTextNode("hprim:nomUsuel", $personnePhysique),
      "prenom"          => $prenoms[0],
      "naissance"       => $xpath->queryTextNode("hprim:date", $elementDateNaissance)
    );
	}
	
  function mappingActesCCAM($data) {
    $node = $data['actesCCAM'];
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $actesCCAM = array();
    foreach ($node->childNodes as $_acteCCAM) {
      $actesCCAM[] = $this->mappingActeCCAM($_acteCCAM, $data);
    }

    return $actesCCAM;
  }
  
  function mappingActeCCAM($node, $data) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    		    
		$acteCCAM = new CActeCCAM();
		$acteCCAM->code_acte     = $xpath->queryTextNode("hprim:codeActe", $node);
		$acteCCAM->code_activite = $xpath->queryTextNode("hprim:codeActivite", $node);
		$acteCCAM->code_phase    = $xpath->queryTextNode("hprim:codePhase", $node);
		$acteCCAM->execution     = $xpath->queryTextNode("hprim:execute/hprim:date", $node)." ".mbTransformTime($xpath->queryTextNode("hprim:execute/hprim:heure", $node), null , "%H:%M:%S");
				
    return array (
      "idSourceIntervention" => $data['idSourceIntervention'],
      "idCibleIntervention"  => $data['idCibleIntervention'],
      "idSourceActeCCAM"     => $data['idSourceActeCCAM'],
      "idCibleActeCCAM"      => $data['idCibleActeCCAM'],
			"acteCCAM"             => $acteCCAM
    );
  }
}

?>