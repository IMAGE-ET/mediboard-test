<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHPrimXMLEvenementsServeurActivitePmsi extends CHPrimXMLEvenements {
  static $evenements = array(
    'evenementPMSI'                => "CHPrimXMLEvenementsPmsi",
    'evenementServeurActe'         => "CHPrimXMLEvenementsServeurActes",
    'evenementServeurEtatsPatient' => "CHPrimXMLEvenementsServeurEtatsPatient",
    'evenementFraisDivers'         => "CHPrimXMLEvenementsFraisDivers",
    'evenementServeurIntervention' => "CHPrimXMLEvenementsServeurIntervention",
  );
  
  static function getHPrimXMLEvenements($messageServeurActivitePmsi) {
    $hprimxmldoc = new CMbXMLDocument();
    $hprimxmldoc->loadXML($messageServeurActivitePmsi);
    
    $xpath = new CMbXPath($hprimxmldoc);
    $event = $xpath->queryUniqueNode("/*/*[2]");

    if ($nodeName = $event->nodeName) {
      return new self::$evenements[$nodeName];
    } 
    
    return new CHPrimXMLEvenementsServeurActivitePmsi();
  }  
  
  function __construct($dirschemaname = null, $schemafilename = null) {
    $this->type = "pmsi";
    
    if (!$this->evenement) {
      return;
    }
    
    $version = CAppUI::conf("hprimxml $this->evenement version");
    // Version 1.01 : schemaPMSI - schemaServeurActe
    if ($version == "1.01") {
      parent::__construct($dirschemaname, $schemafilename."101");
    } 
    // Version 1.04 - 1.05 - 1.06 - 1.07
    else {
      $version = str_replace(".", "", $version);
      parent::__construct("serveurActivitePmsi_v$version", $schemafilename.$version);
    }   
  }
  
  function getEvenements() {
    return self::$evenements;
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
  
  function mappingVenue($node, CSejour $sejour) {
    // On ne récupère que l'entrée et la sortie 
    $sejour = CHPrimXMLEvenementsPatients::getEntree($node, $sejour);
    $sejour = CHPrimXMLEvenementsPatients::getSortie($node, $sejour);
    
    // On ne check pas la cohérence des dates des consults/intervs
    $sejour->_skip_date_consistencies = true;
    
    return $sejour;    
  }
  
  function mappingIntervention($node, COperation $operation) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $debut = $this->getDebutInterv($node);
    $fin   = $this->getFinInterv($node);
    
    // Traitement de la date/heure début, et durée de l'opération
    $operation->temp_operation = mbSubTime(mbTime($debut), mbTime($fin)); 
    $operation->_hour_op       = null;
    $operation->_min_op        = null;

    // Si une intervention du passée    
    if (mbDate($debut) < mbDate()) {
      // On affecte le début de l'opération
      if (!$operation->debut_op) {
        $operation->debut_op = mbTime($debut);
      } 
      // On affecte la fin de l'opération
      if (!$operation->fin_op) {
        $operation->fin_op = mbTime($fin);
      }
    }
    // Si dans le futur
    else {
      $operation->_hour_urgence  = null;
      $operation->_min_urgence   = null;
      $operation->time_operation = mbTime($debut);
    }
    
    $operation->libelle = CMbString::capitalize($xpath->queryTextNode("hprim:libelle", $node));
    $operation->rques   = CMbString::capitalize($xpath->queryTextNode("hprim:commentaire", $node));
    
    // Côté
    $cote = array (
      "D" => "droit",
      "G" => "gauche",
      "B" => "bilatéral",
      "T" => "total",
      "I" => "inconnu"
    );
    $code_cote = $xpath->queryTextNode("hprim:cote/hprim:code", $node);
    $operation->cote = isset($cote[$code_cote]) ? $cote[$code_cote] : ($operation->cote ? $operation->cote : "inconnu");
    
    // Conventionnée ?
    $operation->conventionne = $xpath->queryTextNode("hprim:convention", $node);
    
    // Extemporané
    $indicateurs = $xpath->query("hprim:indicateurs/*", $node);
    foreach ($indicateurs as $_indicateur) {
      if ($xpath->queryTextNode("hprim:code", $_indicateur) == "EXT") {
        $operation->exam_extempo = true;
      }
    }
    
    // TypeAnesthésie
    $this->getTypeAnesthesie($node, $operation);
    
    $operation->duree_uscpo = $xpath->queryTextNode("hprim:dureeUscpo", $node);
  }
  
  function getTypeAnesthesie($node, COperation $operation) {
    $xpath = new CHPrimXPath($node->ownerDocument); 
       
    if (!$typeAnesthesie = $xpath->queryTextNode("hprim:typeAnesthesie", $node)) {
      return;
    }
    
    $operation->type_anesth = CIdSante400::getMatch("CTypeAnesth", $this->_ref_sender->_tag_hprimxml, $typeAnesthesie)->object_id;
  }
  
  function mappingPlage($node, COperation $operation) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $debut = $this->getDebutInterv($node);
    $fin   = $this->getFinInterv($node);
    
    // Traitement de la date/heure début, et durée de l'opération
    $date_op  = mbDate($debut);
    $time_op  = mbTime($debut);
    $temps_op = mbSubTime(mbTime($debut), mbTime($fin)); 
    
    // Recherche d'une éventuelle PlageOp
    $plageOp           = new CPlageOp();  
    $plageOp->chir_id  = $operation->chir_id;
    $plageOp->salle_id = $operation->salle_id;
    $plageOp->date     = $date_op;
    $plageOps          = $plageOp->loadMatchingList();
    foreach ($plageOps as $_plage) {
      // Si notre intervention est dans la plage Mediboard
      if ($_plage->debut <= $time_op && (mbAddTime($temps_op, $time_op) <= $_plage->fin)) {
        $plageOp = $_plage;
        
        break;
      }
    }
    
    if ($plageOp->_id) {
      $operation->plageop_id = $plageOp->_id;
    }
    else {
      // Dans le cas où l'on avait une plage sur l'interv on la supprime
      $operation->plageop_id = "";
      
      $operation->date       = $date_op;
    }
  }
  
  function getDebutInterv($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $this->getDateHeure($xpath->queryUniqueNode("hprim:debut", $node, false));
  }
  
  function getFinInterv($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    return $this->getDateHeure($xpath->queryUniqueNode("hprim:fin", $node, false));
  } 
  
  function getParticipant($node) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    
    $adeli = $xpath->queryTextNode("hprim:participants/hprim:participant/hprim:medecin/hprim:numeroAdeli", $node);
    
    // Recherche du mediuser
    $mediuser = new CMediusers();
    if (!$adeli) {
      return $mediuser;
    }
    
    $mediuser->adeli = $adeli;
    $mediuser->loadMatchingObject();
    
    return $mediuser;
  }
  
  function getSalle($node, CSejour $sejour) {
    $xpath = new CHPrimXPath($node->ownerDocument);
    $name = $xpath->queryTextNode("hprim:uniteFonctionnelle/hprim:code", $node);
    
    // Recherche de la salle
    $salle = new CSalle();
    
    $where = array(
      "sallesbloc.nom"           => $salle->_spec->ds->prepare("=%", $name),
      "bloc_operatoire.group_id" => "= '$sejour->group_id'"
    );
    $ljoin = array(
      "bloc_operatoire" => "bloc_operatoire.bloc_operatoire_id = sallesbloc.bloc_id"
    );
    
    $salle->loadObject($where, null, null, $ljoin);
            
    return $salle;
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
  
  function handle(CHPrimXMLAcquittementsServeurActivitePmsi $dom_acq, CMbObject $mbObject, $data) {
  }
}

?>