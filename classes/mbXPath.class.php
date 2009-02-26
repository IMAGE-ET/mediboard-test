<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Yohann  
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * The CMbXPath class
 */
class CMbXPath extends DOMXPath {
  function __construct(DOMDocument $doc) {
    parent::__construct($doc);
  }

  function queryUniqueNode($query, DOMNode $contextNode = null) {
    $query = utf8_encode($query);
    $nodeList = $contextNode ? parent::query($query, $contextNode) : parent::query($query);
    if ($nodeList->length > 1) {
      trigger_error("queried node is not unique, found $nodeList->length occurence(s) for '$query'", E_USER_WARNING);
      return null;
    }
    return $nodeList->item(0);
  } 
  
  function queryNumcharNode($query, DOMNode $contextNode, $length) {
    if (null == $text = $this->queryTextNode($query, $contextNode, " /-.")) {
      return;
    }
    
    $text = substr($text, 0, $length);
    $text = str_pad($text, $length, "0", STR_PAD_LEFT);
    $text = strtr($text, "O", "0"); // Usual trick
    return $text;
  }
  
  function queryTextNode($query, DOMNode $contextNode, $purgeChars = "") {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = utf8_decode($node->textContent);
      $text = str_replace(str_split($purgeChars), "", $text);
      $text = trim($text);
      $text = addslashes($text);
    }

    return $text;
  } 

  function queryMultilineTextNode($query, DOMNode $contextNode, $prefix = "") {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = utf8_decode($node->textContent);
      if ($prefix) {
        $text = str_replace($prefix, "", $text);
      }
    } 
    
    return $text;
  }
  
  function queryAttributNode($query, DOMNode $contextNode, $attName, $purgeChars = "") {
    $text = "";
    if ($node = $this->queryUniqueNode($query, $contextNode)) {
      $text = utf8_decode($node->getAttribute($attName));
      $text = str_replace(str_split($purgeChars), "", $text);
      $text = trim($text);
      $text = addslashes($text);
    }

    return $text;
  }
  
  function getMultipleTextNodes($query, DOMNode $contextNode) {
    $array = array();
    $query = utf8_encode($query);
    $nodeList = $contextNode ? parent::query($query, $contextNode) : parent::query($query);
    
    foreach ($nodeList as $n) {
      $array[] = utf8_decode($n->nodeValue);
    }
    return $array;
  }
  
  function getActionEvenement($node) {
    return $this->queryAttributNode("hprim:enregistrementPatient", $node, "action");    
  }
  
  function getIdSource($node) {
    $identifiant = $this->queryUniqueNode("hprim:identifiant", $node);
    $emetteur = $this->queryUniqueNode("hprim:emetteur", $identifiant);
    $referentEmetteur = $this->queryAttributNode("hprim:emetteur", $node, "referent");
    return $this->queryTextNode("hprim:valeur", $emetteur);
  }
  
  function getIdCible($node) {
    $identifiant = $this->queryUniqueNode("hprim:identifiant", $node);
    $recepteur = $this->queryUniqueNode("hprim:recepteur", $identifiant);
    $referentRecepteur = $this->queryAttributNode("hprim:recepteur", $node, "referent");
    return $this->queryTextNode("hprim:valeur", $recepteur);
  }
  
  function createPatient($node, $mbPatient) {
    $mbPatient = $this->getPersonnePhysique($node, $mbPatient);
    $mbPatient = $this->getActiviteSocioProfessionnelle($node, $mbPatient);
    $mbPatient = $this->getPersonnesPrevenir($node, $mbPatient);
    
    return $mbPatient;
  }
  
  function getPersonnePhysique($node, $mbPatient) {
    // Création de l'element personnePhysique
    $personnePhysique = $this->queryUniqueNode("hprim:personnePhysique", $node);
    $sexe = $this->queryAttributNode("hprim:personnePhysique", $node, "sexe");
    $sexeConversion = array (
        "M" => "m",
        "F" => "f",
    );
    $mbPatient->sexe = $sexeConversion[$sexe];
    $mbPatient->nom = $this->queryTextNode("hprim:nomUsuel", $personnePhysique);
    $mbPatient->nom_jeune_fille = $this->queryTextNode("hprim:nomNaissance", $personnePhysique);
    $prenoms = $this->getMultipleTextNodes("hprim:prenoms/*", $personnePhysique);
    $mbPatient->prenom = $prenoms[0];
    $mbPatient->prenom_2 = isset($prenoms[1]) ? $prenoms[1] : "";
    $mbPatient->prenom_3 = isset($prenoms[2]) ? $prenoms[2] : "";
    
    $adresses = $this->queryUniqueNode("hprim:adresses", $personnePhysique);
    $adresse = $this->queryUniqueNode("hprim:adresse", $adresses);
    $mbPatient->adresse = $this->queryTextNode("hprim:ligne", $adresse);
    $mbPatient->ville = $this->queryTextNode("hprim:ville", $adresse);
    $mbPatient->pays_insee = $this->queryTextNode("hprim:pays", $adresse);
    $mbPatient->cp = $this->queryTextNode("hprim:codePostal", $adresse);
    
    $telephones = $this->getMultipleTextNodes("hprim:telephones/*", $personnePhysique);
    $mbPatient->tel = isset($telephones[0]) ? $telephones[0] : "";
    $mbPatient->tel2 = isset($telephones[1]) ? $telephones[1] : "";
    
    $emails = $this->getMultipleTextNodes("hprim:emails/*", $personnePhysique);
    $mbPatient->email = isset($emails[0]) ? $emails[0] : "";
    
    $elementDateNaissance = $this->queryUniqueNode("hprim:dateNaissance", $personnePhysique);
    $mbPatient->naissance = $this->queryTextNode("hprim:date", $elementDateNaissance);
    
    $lieuNaissance = $this->queryUniqueNode("hprim:lieuNaissance", $personnePhysique);
    $mbPatient->lieu_naissance = $this->queryTextNode("hprim:ville", $lieuNaissance);
    $mbPatient->pays_naissance_insee = $this->queryTextNode("hprim:pays", $lieuNaissance);
    $mbPatient->cp_naissance = $this->queryTextNode("hprim:codePostal", $lieuNaissance);
    
    return $mbPatient;
  }
  
  function getActiviteSocioProfessionnelle($node, $mbPatient) {
    $mbPatient->profession = $this->queryTextNode("hprim:activiteSocioProfessionnelle", $node); 
    
    return $mbPatient;
  }
  
  function getPersonnesPrevenir($node, $mbPatient) {
    $personnesPrevenir = $this->query("hprim:personnesPrevenir/*", $node);
    foreach ($personnesPrevenir as $personnePrevenir) {
    	$mbPatient->prevenir_nom = $this->queryTextNode("hprim:nomUsuel", $personnePrevenir);
	    $prenoms = $this->getMultipleTextNodes("hprim:prenoms/*", $personnePrevenir);
	    $mbPatient->prevenir_prenom = $prenoms[0];
	    
	    $adresses = $this->queryUniqueNode("hprim:adresses", $personnePrevenir);
	    $adresse = $this->queryUniqueNode("hprim:adresse", $adresses);
	    $mbPatient->prevenir_adresse = $this->queryTextNode("hprim:ligne", $adresse);
	    $mbPatient->prevenir_ville = $this->queryTextNode("hprim:ville", $adresse);
	    $mbPatient->prevenir_cp = $this->queryTextNode("hprim:codePostal", $adresse);
	    
	    $telephones = $this->getMultipleTextNodes("hprim:telephones/*", $personnePrevenir);
	    $mbPatient->prevenir_tel = isset($telephones[0]) ? $telephones[0] : "";
    }
        
    return $mbPatient;
  }
}

?>