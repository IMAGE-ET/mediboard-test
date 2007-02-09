<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author Romain Ollivier
 */

class CCodeCCAM {
  // Variables de structure 
  // Id de la base de données (qui doit être dans le config.php)
  var $dbccam = null;
  // Code de l'acte
  var $code = null; 
  // Chapitres de la CCAM concernes
  var $chapitres = null;
  // Libelles
  var $libelleCourt = null;
  var $libelleLong = null;
  // Place dans la CCAM
  var $place = null;
  // Remarques sur l'acte
  var $remarques = null;
  // Activites correspondantes
  var $activites = array();
  // Nombre de phases par activités
  var $phases = array();
  // Incompatibilite
  var $incomps = array(); 
  // Associabilite
  var $assos = null;
  // Procedure
  var $procedure = null; 
  
  // Variable calculées
  var $_code7 = null;
  
  // Constructeur
  function CCodeCCAM($code) {
    global $AppUI;
    $this->dbccam = $AppUI->cfg["baseCCAM"];
    do_connect($this->dbccam);
    $this->code = strtoupper($code);
  }
  
  // Chargement des variables importantes
  function LoadLite() {
    $query = db_prepare("select * from actes where CODE = %", $this->code);
    $result = db_exec($query, $this->dbccam);
    if(mysql_num_rows($result) == 0) {
      $this->code = "-";
      //On rentre les champs de la table actes
      $this->libelleCourt = "";
      $this->libelleLong = "";
      $this->_code7 = 1;
    } else {
      $row = db_fetch_array($result);
      //On rentre les champs de la table actes
      $this->libelleCourt = $row["LIBELLECOURT"];
      $this->libelleLong = $row["LIBELLELONG"];
      $query1 = "select * from activiteacte where ";
      $query1 .= db_prepare("CODEACTE = %", $this->code);
      $query1 .= "and ACTIVITE = '4'";
      $result1 = db_exec($query1, $this->dbccam);
      if(db_num_rows($result1)) {
        $query2 = "select * from modificateuracte where ";
        $query2 .= db_prepare("CODEACTE = %", $this->code);
        $query2 .= "and CODEACTIVITE = '4'";
        $query2 .= "and MODIFICATEUR = '7'";
        $result2 = db_exec($query2, $this->dbccam);
        $this->_code7 = db_num_rows($result2);
      } else
        $this->_code7 = 1;
    }
  }
  
  function loadChaps() {
    $query = db_prepare("select * from actes where CODE = %", $this->code);
    $result = db_exec($query, $this->dbccam);
    $row = db_fetch_array($result);

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
      $query = db_prepare("select * from arborescence where CODEPERE = %1 and rang = %2", $pere, $rang);
      $result = db_exec($query, $this->dbccam);
      $row = db_fetch_array($result);
      
      $query = db_prepare("select * from notesarborescence where CODEMENU = %", $row["CODEMENU"]);
      $result2 = db_exec($query, $this->dbccam);
      
      $track .= substr($row["RANG"], -2) . ".";
      $this->chapitres[$key]["rang"] = $track;
      $this->chapitres[$key]["code"] = $row["CODEMENU"];
      $this->chapitres[$key]["nom"] = $row["LIBELLE"];
      $this->chapitres[$key]["rq"] = "";
      while($row2 = db_fetch_array($result2)) {
        $this->chapitres[$key]["rq"] .= "* " . str_replace("¶", "\n", $row2["TEXTE"]) . "\n";
      }
      $pere = $this->chapitres[$key]["code"];
    }
    $this->place = $this->chapitres[3]["rang"];
  }
   
  // Chargement des variables
  function Load() {
    $query = db_prepare("select * from actes where CODE = %", $this->code);
    $result = db_exec($query, $this->dbccam);
    if(db_num_rows($result) == 0) {
      $this->code = "";
      //On rentre les champs de la table actes
      $this->libelleCourt = "";
      $this->libelleLong = "";
    } else {
      $row = db_fetch_array($result);
    
      //On rentre les champs de la table actes
      $this->libelleCourt = $row["LIBELLECOURT"];
      $this->libelleLong = $row["LIBELLELONG"];
    
      //On rentre les caracteristiques des chapitres
      $this->loadChaps();
      
      // Extraction des remarques
      $this->remarques = array();
      $query = db_prepare("select * from notes where CODEACTE = %", $this->code);
      $result = db_exec($query, $this->dbccam);
      while ($row = db_fetch_array($result)) {
        $this->remarques[] = str_replace("¶", "\n", $row["TEXTE"]);
      }
      
      // Extraction des activités
      $query = "select ACTIVITE as numero " .
          "\nfrom activiteacte " .
          "\nwhere CODEACTE = %";
      $query = db_prepare($query, $this->code);
      $result = db_exec($query, $this->dbccam);
      while($obj = db_fetch_object($result)) {
        $obj->libelle = "";
        $this->activites[$obj->numero] = $obj;
      }
      
      // Libellés des activités
      foreach($this->remarques as $remarque) {
        $match = null;
        if (preg_match("/Activité (\d) : (.*)/i", $remarque, $match)) {
          $this->activites[$match[1]]->libelle = $match[2];
        }
      }
      
      // Détail des activités
      foreach($this->activites as $key => $value) {
        $activite =& $this->activites[$key];
  
        // Type de l'activité
        $query = "select LIBELLE as `type`" .
            "\nfrom activite " .
            "\nwhere CODE = %";
        $query = db_prepare($query, $activite->numero);
        $result = db_exec($query, $this->dbccam);
        $obj = db_fetch_object($result);
        $activite->type = $obj->type;
  
        // Extraction des modificateurs
        $activite->modificateurs = array();
        $modificateurs =& $activite->modificateurs;
        $query = "select * from modificateuracte " .
            "\nwhere CODEACTE = %1" .
            "\nand CODEACTIVITE = %2";
        $query = db_prepare($query, $this->code, $activite->numero);
        $result = db_exec($query, $this->dbccam);
        
        while($row = db_fetch_array($result)) {
          $query = "select CODE as code, LIBELLE as libelle" .
              "\nfrom modificateur " .
              "\nwhere CODE = %" .
              "\norder by CODE";
          $query = db_prepare($query, $row["MODIFICATEUR"]);
          $modificateurs[] = db_fetch_object(db_exec($query, $this->dbccam));
        }
  
        // Extraction des phases
        $activite->phases = array();
        $phases =& $activite->phases;
        $query = "select PHASE as phase, PRIXUNITAIRE as tarif" .
            "\nfrom phaseacte " .
            "\nwhere CODEACTE = %1" .
            "\nand ACTIVITE = %2" .
            "\norder by PHASE";
        $query = db_prepare($query, $this->code, $activite->numero);
        $result = db_exec($query, $this->dbccam);
              
        while($obj = db_fetch_object($result)) {
          $phases[$obj->phase] = $obj;
          $phase =& $phases[$obj->phase];
          $phase->tarif = floatval($obj->tarif)/100;
          $phase->libelle = "Phase Principale";
          
          // Copie des modificateurs pour chaque phase. Utile pour dPsalleOp
          $phase->_modificateurs = $modificateurs;
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
      
      //On rentre les actes associés
      $query = db_prepare("select * from associabilite where CODEACTE = % group by ACTEASSO", $this->code);
      $result = db_exec($query, $this->dbccam);
      $i = 0;
      while($row = db_fetch_array($result)) {
        $this->assos[$i]["code"] = $row["ACTEASSO"];
        $query = db_prepare("select * from actes where CODE = %", $row["ACTEASSO"]);
        $result2 = db_exec($query, $this->dbccam);
        $row2 = db_fetch_array($result2);
        $this->assos[$i]["texte"] = $row2["LIBELLELONG"];
        $i++;
      }
      
      //On rentre les actes incompatibles
      $query = db_prepare("select * from incompatibilite where CODEACTE = % group by INCOMPATIBLE", $this->code);
      $result = db_exec($query, $this->dbccam);
      $i = 0;
      while($row = db_fetch_array($result)) {
        $this->incomps[$i]["code"] = $row["INCOMPATIBLE"];
        $query = db_prepare("select * from actes where CODE = %", $row["INCOMPATIBLE"]);
        $result2 = db_exec($query, $this->dbccam);
        $row2 = db_fetch_array($result2);
        $this->incomps[$i]["texte"] = $row2["LIBELLELONG"];
        $i++;
      }
      
      //On rentre la procédure associée
      $query = db_prepare("select * from procedures where CODEACTE = %", $this->code);
      $result = db_exec($query, $this->dbccam);
      if(db_num_rows($result) > 0) {
        $row = db_fetch_array($result);
        $this->procedure["code"] = $row["CODEPROCEDURE"];
        $query = db_prepare("select LIBELLELONG from actes where CODE = %", $this->procedure["code"]);
        $result = db_exec($query, $this->dbccam);
        $row = db_fetch_array($result);
        $this->procedure["texte"] = $row["LIBELLELONG"];
      } else {
        $this->procedure["code"] = "aucune";
        $this->procedure["texte"] = "";
      }
    }
  }
} 

?>