<?php /* $Id: acte.class.php 3034 2007-12-06 10:56:42Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision: 3034 $
 * @author Romain Ollivier
 */



class CCodeCCAM {
  var $code          = null; // Code de l'acte 
  var $chapitres     = null; // Chapitres de la CCAM concernes
  var $libelleCourt  = null; // Libelles
  var $libelleLong   = null;
  var $place         = null; // Place dans la CCAM
  var $remarques     = null; // Remarques sur le code
  var $activites     = array(); // Activites correspondantes
  var $phases        = array(); // Nombre de phases par activités
  var $incomps       = array(); // Incompatibilite
  var $assos         = array(); // Associabilite
  var $procedure     = null; // Procedure
  var $remboursement = null; // Remboursement
  
  // Variable calculées
  var $_code7 = null; // Possibilité d'ajouter le modificateur 7 (0 : non, 1 : oui)
  var $_default  = null;
  
  // Activités et phases recuperées depuis le code CCAM
  var $_activite = null;
  var $_phase    = null;
  
  
  /**
   * Constructeur à partir du code CCAM
   */
  function CCodeCCAM($code) {
    // Static initialisation
    static $spec = null;
    if (!$spec) {
      $spec = new CMbObjectSpec();
      $spec->dsn = "ccamV2";
      $spec->init();
    }
    
    $this->_spec =& $spec;
    
    if(strlen($code) > 7){
    // Cas ou l'activite et la phase sont indiquées dans le code (ex: BFGA004-1-0)
      $detailCode = explode("-", $code);
      $this->code = strtoupper($detailCode[0]);
      $this->_activite = $detailCode[1];
      if(count($detailCode) > 2){
        $this->_phase = $detailCode[2];
      }
    } else {
      $this->code = strtoupper($code);
    }
  }

  // Chargement des variables obligatoires
  function LoadLite() {
    if($this->getLibelles()) {
      $this->getActivite7();
      $this->getTarification();
    }
  }

  // Chargement des variables importantes
  function LoadMedium() {
    if($this->getLibelles()) {
      $this->getTarification();
      $this->getChaps();
      $this->getRemarques();
      $this->getActivites();
    }
  }
   
  // Chargement de toutes les variables
  function Load() {
    if($this->getLibelles()) {
      $this->getTarification();
      $this->getChaps();
      $this->getRemarques();
      $this->getActivites();
      $this->getActesAsso();
      $this->getActesIncomp();
      $this->getProcedure();
    }
  }
  
  function getLibelles() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $this->code);
    $result = $ds->exec($query);
    if(mysql_num_rows($result) == 0) {
      $this->code = "-";
      //On rentre les champs de la table actes
      $this->libelleCourt = "Acte inconnu ou supprimé";
      $this->libelleLong = "Acte inconnu ou supprimé";
      $this->_code7 = 1;
      return false;
    } else {
      $row = $ds->fetchArray($result);
      //On rentre les champs de la table actes
      $this->libelleCourt = $row["LIBELLECOURT"];
      $this->libelleLong = $row["LIBELLELONG"];
      return true;
    }
  }
  
  function getActivite7() {
    $ds =& $this->_spec->ds;
    // recherche de la dernière date d'effet
    $query1 = "SELECT MAX(DATEEFFET) as LASTDATE FROM modificateuracte WHERE ";
    $query1 .= $ds->prepare("CODEACTE = %", $this->code);
    $query1 .= " AND CODEACTIVITE = '4'";
    $query1 .= " GROUP BY CODEACTE";
    $result1 = $ds->exec($query1);
    // Chargement des modificateurs
    if($ds->numRows($result1)) {
      $row = $ds->fetchArray($result1);
      $lastDate = $row["LASTDATE"];
      $query2 = "SELECT * FROM modificateuracte WHERE ";
      $query2 .= $ds->prepare("CODEACTE = %", $this->code);
      $query2 .= " AND CODEACTIVITE = '4'";
      $query2 .= " AND MODIFICATEUR = '7'";
      $query2 .= " AND DATEEFFET = '$lastDate'";
      $result2 = $ds->exec($query2);
      $this->_code7 = $ds->numRows($result2);
    } else {
      $this->_code7 = 1;
    }
  }
  
  function getTarification() {
    $ds =& $this->_spec->ds;
    $query = "SELECT * FROM infotarif WHERE ";
    $query .= $ds->prepare("CODEACTE = %", $this->code);
    $query .= " ORDER BY DATEEFFET DESC";
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->remboursement = $row["REMBOURSEMENT"];
  }  
  
  function getChaps() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $this->code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);

    // On rentre les champs de la table actes
    $this->chapitres[0]["db"] = $row["ARBORESCENCE1"];
    $this->chapitres[1]["db"] = $row["ARBORESCENCE2"];
    $this->chapitres[2]["db"] = $row["ARBORESCENCE3"];
    $this->chapitres[3]["db"] = $row["ARBORESCENCE4"];
    $pere = "000001";
    $track = "";
    
    // On rentre les infos sur les chapitres
    foreach($this->chapitres as $key => $value) {
      $rang = $this->chapitres[$key]["db"];
      $query = $ds->prepare("SELECT * FROM arborescence WHERE CODEPERE = %1 AND rang = %2", $pere, $rang);
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);
      
      $query = $ds->prepare("SELECT * FROM notesarborescence WHERE CODEMENU = %", $row["CODEMENU"]);
      $result2 = $ds->exec($query);
      
      $track .= substr($row["RANG"], -2) . ".";
      $this->chapitres[$key]["rang"] = $track;
      $this->chapitres[$key]["code"] = $row["CODEMENU"];
      $this->chapitres[$key]["nom"] = $row["LIBELLE"];
      $this->chapitres[$key]["rq"] = "";
      while($row2 = $ds->fetchArray($result2)) {
        $this->chapitres[$key]["rq"] .= "* " . str_replace("¶", "\n", $row2["TEXTE"]) . "\n";
      }
      $pere = $this->chapitres[$key]["code"];
    }
    $this->place = $this->chapitres[3]["rang"];
  }
  
  function getRemarques() {
    $ds =& $this->_spec->ds;
    $this->remarques = array();
    $query = $ds->prepare("SELECT * FROM notes WHERE CODEACTE = %", $this->code);
    $result = $ds->exec($query);
    while ($row = $ds->fetchArray($result)) {
      $this->remarques[] = str_replace("¶", "\n", $row["TEXTE"]);
    }
  }
  
  function getActivites() {
    $ds =& $this->_spec->ds;
    // Extraction des activités
    $query = "SELECT ACTIVITE AS numero " .
        "\nFROM activiteacte " .
        "\nWHERE CODEACTE = %";
    $query = $ds->prepare($query, $this->code);
    $result = $ds->exec($query);
    while($obj = $ds->fetchObject($result)) {
      // Test si l'activité 1 est virtuelle
      $virtuelle = true;
      $virtuelle &= $this->chapitres[0] == "18";
      $virtuelle &= $this->chapitres[1] == "01";
      $virtuelle &= $obj->numero == 1;
      if(!$virtuelle) {
        $obj->libelle = "";
        $this->activites[$obj->numero] = $obj;
      }
    }
    // Libellés des activités
    foreach($this->remarques as $remarque) {
      $match = null;
      if (preg_match("/Activité (\d) : (.*)/i", $remarque, $match)) {
        $this->activites[$match[1]]->libelle = $match[2];
      }
    }
    // Détail des activités
    foreach($this->activites as &$activite) {
      // Type de l'activité
      $query = "SELECT LIBELLE AS `type`" .
          "\nFROM activite " .
          "\nWHERE CODE = %";
      $query = $ds->prepare($query, $activite->numero);
      $result = $ds->exec($query);
      $obj = $ds->fetchObject($result);
      $activite->type = $obj->type;
      // Modificateurs de l'activite
      $this->getModificateursFromActivite($activite);
      $this->getPhasesFromActivite($activite);
    }
    $this->_default = reset($this->activites);
    if($this->_default->phases){
      $this->_default = $this->_default->phases[0]->tarif;
    } else {
    	$this->_default = 0;
    }
  }
  
  function getModificateursFromActivite(&$activite) {
    $ds =& $this->_spec->ds;
    // recherche de la dernière date d'effet
    $query = "SELECT MAX(DATEEFFET) AS LASTDATE".
        "\nFROM modificateuracte" .
        "\nWHERE CODEACTE = %1" .
        "\nAND CODEACTIVITE = %2" .
        "\nGROUP BY CODEACTE";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $lastDate = $row["LASTDATE"];
    // Extraction des modificateurs
    $activite->modificateurs = array();
    $modificateurs =& $activite->modificateurs;
    $query = "SELECT * FROM modificateuracte " .
        "\nWHERE CODEACTE = %1" .
        "\nAND CODEACTIVITE = %2" .
        "\nAND DATEEFFET = '$lastDate'" .
        "\nGROUP BY MODIFICATEUR";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);
    
    while($row = $ds->fetchArray($result)) {
      $query = "SELECT CODE AS code, LIBELLE AS libelle" .
          "\nFROM modificateur " .
          "\nWHERE CODE = %" .
          "\nORDER BY CODE";
      $query = $ds->prepare($query, $row["MODIFICATEUR"]);
      $modificateurs[] = $ds->fetchObject($ds->exec($query));
    }
  }
  
  function getPhasesFromActivite(&$activite) {
    $ds =& $this->_spec->ds;
    // Extraction des phases
    $activite->phases = array();
    $phases =& $activite->phases;
    $query = "SELECT PHASE AS phase, PRIXUNITAIRE AS tarif" .
        "\nFROM phaseacte " .
        "\nWHERE CODEACTE = %1" .
        "\nAND ACTIVITE = %2" .
        "\nGROUP BY PHASE" .
        "\nORDER BY PHASE, DATE1 DESC";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);
          
    while($obj = $ds->fetchObject($result)) {
      $phases[$obj->phase] = $obj;
      $phase =& $phases[$obj->phase];
      $phase->tarif = floatval($obj->tarif)/100;
      $phase->libelle = "Phase Principale";
      
      // Copie des modificateurs pour chaque phase. Utile pour dPsalleOp
      $phase->_modificateurs = $activite->modificateurs;
    }
    
    // Libellés des phases
    foreach($this->remarques as $remarque) {
      if (preg_match("/Phase (\d) : (.*)/i", $remarque, $match)) {
        if (isset($phases[$match[1]])) {
          $phases[$match[1]]->libelle = $match[2];
        }
      }
    }
  }
  
  function getActesAsso() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM associabilite WHERE CODEACTE = % GROUP BY ACTEASSO LIMIT 0, 15", $this->code);
    $result = $ds->exec($query);
    $i = 0;
    while($row = $ds->fetchArray($result)) {
      $this->assos[$i]["code"] = $row["ACTEASSO"];
      $query2 = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $row["ACTEASSO"]);
      $result2 = $ds->exec($query2);
      $row2 = $ds->fetchArray($result2);
      $this->assos[$i]["texte"] = $row2["LIBELLELONG"];
      $i++;
    }
  }
  
  function getActesIncomp() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM incompatibilite WHERE CODEACTE = % GROUP BY INCOMPATIBLE LIMIT 0, 15", $this->code);
    $result = $ds->exec($query);
    $i = 0;
    while($row = $ds->fetchArray($result)) {
      $this->incomps[$i]["code"] = $row["INCOMPATIBLE"];
      $query2 = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $row["INCOMPATIBLE"]);
      $result2 = $ds->exec($query2);
      $row2 = $ds->fetchArray($result2);
      $this->incomps[$i]["texte"] = $row2["LIBELLELONG"];
      $i++;
    }
  }
  
  function getProcedure() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM procedures WHERE CODEACTE = % GROUP BY CODEACTE ORDER BY DATEEFFET DESC", $this->code);
    $result = $ds->exec($query);
    if($ds->numRows($result) > 0) {
      $row = $ds->fetchArray($result);
      $this->procedure["code"] = $row["CODEPROCEDURE"];
      $query2 = $ds->prepare("SELECT LIBELLELONG FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $this->procedure["code"]);
      $result2 = $ds->exec($query2);
      $row2 = $ds->fetchArray($result2);
      $this->procedure["texte"] = $row2["LIBELLELONG"];
    } else {
      $this->procedure["code"] = "aucune";
      $this->procedure["texte"] = "";
    }
  }
  
  function getForfait($modificateur) {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM modificateurforfait WHERE CODE = % AND DATEFIN = '00000000'", $modificateur);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $valeur = array();
    $valeur["forfait"] = $row["FORFAIT"] / 100;
    $valeur["coefficient"] = $row["COEFFICIENT"] / 10;
    return $valeur;
  }
  
  function getCoeffAsso($code) {
    if($code == "X")
      return 0;
    if(!$code) {
      return 100;
    }
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM association WHERE CODE = % AND DATEFIN = '00000000'", $code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $valeur = $row["COEFFICIENT"] / 10;
    return $valeur;
  }
}



?>