<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author Romain Ollivier
 */



class CCodeCCAM {
  // Code de l'acte
  var $code = null; 
  // Chapitres de la CCAM concernes
  var $chapitres = null;
  // Libelles
  var $libelleCourt = null;
  var $libelleLong = null;
  // Place dans la CCAM
  var $place = null;
  // Remarques sur le code
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
  
  var $_default = null;
  
  /**
   * Construction
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

    $this->code = strtoupper($code);
  }
  
  // Chargement des variables importantes
  function LoadLite() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("select * from actes where CODE = %", $this->code);
    $result = $ds->exec($query);
    if(mysql_num_rows($result) == 0) {
      $this->code = "-";
      //On rentre les champs de la table actes
      $this->libelleCourt = "";
      $this->libelleLong = "";
      $this->_code7 = 1;
    } else {
      $row = $ds->fetchArray($result);
      //On rentre les champs de la table actes
      $this->libelleCourt = $row["LIBELLECOURT"];
      $this->libelleLong = $row["LIBELLELONG"];
      $query1 = "select * from activiteacte where ";
      $query1 .= $ds->prepare("CODEACTE = %", $this->code);
      $query1 .= "and ACTIVITE = '4'";
      $result1 = $ds->exec($query1);
      if($ds->numRows($result1)) {
        $query2 = "select * from modificateuracte where ";
        $query2 .= $ds->prepare("CODEACTE = %", $this->code);
        $query2 .= "and CODEACTIVITE = '4'";
        $query2 .= "and MODIFICATEUR = '7'";
        $result2 = $ds->exec($query2);
        $this->_code7 = $ds->numRows($result2);
      } else
        $this->_code7 = 1;
    }
  }
  
  
  // Chargement des variables
  function LoadMedium() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("select * from actes where CODE = %", $this->code);
    $result = $ds->exec($query);
    if($ds->numRows($result) == 0) {
      $this->code = "";
      //On rentre les champs de la table actes
      $this->libelleCourt = "";
      $this->libelleLong = "";
    } else {
      $row = $ds->fetchArray($result);
    
      //On rentre les champs de la table actes
      $this->libelleCourt = $row["LIBELLECOURT"];
      $this->libelleLong = $row["LIBELLELONG"];
    
      //On rentre les caracteristiques des chapitres
      $this->loadChaps();
      
      // Extraction des remarques
      $this->remarques = array();
      $query = $ds->prepare("select * from notes where CODEACTE = %", $this->code);
      $result = $ds->exec($query);
      while ($row = $ds->fetchArray($result)) {
        $this->remarques[] = str_replace("¶", "\n", $row["TEXTE"]);
      }
      
      // Extraction des activités
      $query = "select ACTIVITE as numero " .
          "\nfrom activiteacte " .
          "\nwhere CODEACTE = %";
      $query = $ds->prepare($query, $this->code);
      $result = $ds->exec($query);
      while($obj = $ds->fetchObject($result)) {
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
        $query = $ds->prepare($query, $activite->numero);
        $result = $ds->exec($query);
        $obj = $ds->fetchObject($result);
        $activite->type = $obj->type;
  
        // Extraction des modificateurs
        $activite->modificateurs = array();
        $modificateurs =& $activite->modificateurs;
        $query = "select * from modificateuracte " .
            "\nwhere CODEACTE = %1" .
            "\nand CODEACTIVITE = %2";
        $query = $ds->prepare($query, $this->code, $activite->numero);
        $result = $ds->exec($query);
        
        while($row = $ds->fetchArray($result)) {
          $query = "select CODE as code, LIBELLE as libelle" .
              "\nfrom modificateur " .
              "\nwhere CODE = %" .
              "\norder by CODE";
          $query = $ds->prepare($query, $row["MODIFICATEUR"]);
          $modificateurs[] = $ds->fetchObject($ds->exec($query));
        }
  
        // Extraction des phases
        $activite->phases = array();
        $phases =& $activite->phases;
        $query = "select PHASE as phase, PRIXUNITAIRE as tarif" .
            "\nfrom phaseacte " .
            "\nwhere CODEACTE = %1" .
            "\nand ACTIVITE = %2" .
            "\norder by PHASE";
        $query = $ds->prepare($query, $this->code, $activite->numero);
        $result = $ds->exec($query);
              
        while($obj = $ds->fetchObject($result)) {
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
      $this->_default = reset($this->activites);
      if($this->_default->phases){
        $this->_default = $this->_default->phases[0]->tarif;
      }
      else {
      	$this->_default = 0;
      }
    }
  }
  
    
  
  
  
  function loadChaps() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("select * from actes where CODE = %", $this->code);
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
      $query = $ds->prepare("select * from arborescence where CODEPERE = %1 and rang = %2", $pere, $rang);
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);
      
      $query = $ds->prepare("select * from notesarborescence where CODEMENU = %", $row["CODEMENU"]);
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
   
  // Chargement des variables
  function Load() {
    $ds =& $this->_spec->ds;
    $query = $ds->prepare("select * from actes where CODE = %", $this->code);
    $result = $ds->exec($query);
    if($ds->numRows($result) == 0) {
      $this->code = "";
      //On rentre les champs de la table actes
      $this->libelleCourt = "";
      $this->libelleLong = "";
    } else {
      $row = $ds->fetchArray($result);
    
      //On rentre les champs de la table actes
      $this->libelleCourt = $row["LIBELLECOURT"];
      $this->libelleLong = $row["LIBELLELONG"];
    
      //On rentre les caracteristiques des chapitres
      $this->loadChaps();
      
      // Extraction des remarques
      $this->remarques = array();
      $query = $ds->prepare("select * from notes where CODEACTE = %", $this->code);
      $result = $ds->exec($query);
      while ($row = $ds->fetchArray($result)) {
        $this->remarques[] = str_replace("¶", "\n", $row["TEXTE"]);
      }
      
      // Extraction des activités
      $query = "select ACTIVITE as numero " .
          "\nfrom activiteacte " .
          "\nwhere CODEACTE = %";
      $query = $ds->prepare($query, $this->code);
      $result = $ds->exec($query);
      while($obj = $ds->fetchObject($result)) {
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
        $query = $ds->prepare($query, $activite->numero);
        $result = $ds->exec($query);
        $obj = $ds->fetchObject($result);
        $activite->type = $obj->type;
  
        // Extraction des modificateurs
        $activite->modificateurs = array();
        $modificateurs =& $activite->modificateurs;
        $query = "select * from modificateuracte " .
            "\nwhere CODEACTE = %1" .
            "\nand CODEACTIVITE = %2";
        $query = $ds->prepare($query, $this->code, $activite->numero);
        $result = $ds->exec($query);
        
        while($row = $ds->fetchArray($result)) {
          $query = "select CODE as code, LIBELLE as libelle" .
              "\nfrom modificateur " .
              "\nwhere CODE = %" .
              "\norder by CODE";
          $query = $ds->prepare($query, $row["MODIFICATEUR"]);
          $modificateurs[] = $ds->fetchObject($ds->exec($query));
        }
  
        // Extraction des phases
        $activite->phases = array();
        $phases =& $activite->phases;
        $query = "select PHASE as phase, PRIXUNITAIRE as tarif" .
            "\nfrom phaseacte " .
            "\nwhere CODEACTE = %1" .
            "\nand ACTIVITE = %2" .
            "\norder by PHASE";
        $query = $ds->prepare($query, $this->code, $activite->numero);
        $result = $ds->exec($query);
              
        while($obj = $ds->fetchObject($result)) {
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
      $query = $ds->prepare("select * from associabilite where CODEACTE = % group by ACTEASSO", $this->code);
      $result = $ds->exec($query);
      $i = 0;
      while($row = $ds->fetchArray($result)) {
        $this->assos[$i]["code"] = $row["ACTEASSO"];
        $query = $ds->prepare("select * from actes where CODE = %", $row["ACTEASSO"]);
        $result2 = $ds->exec($query);
        $row2 = $ds->fetchArray($result2);
        $this->assos[$i]["texte"] = $row2["LIBELLELONG"];
        $i++;
      }
      
      //On rentre les actes incompatibles
      $query = $ds->prepare("select * from incompatibilite where CODEACTE = % group by INCOMPATIBLE", $this->code);
      $result = $ds->exec($query);
      $i = 0;
      while($row = $ds->fetchArray($result)) {
        $this->incomps[$i]["code"] = $row["INCOMPATIBLE"];
        $query = $ds->prepare("select * from actes where CODE = %", $row["INCOMPATIBLE"]);
        $result2 = $ds->exec($query);
        $row2 = $ds->fetchArray($result2);
        $this->incomps[$i]["texte"] = $row2["LIBELLELONG"];
        $i++;
      }
      
      //On rentre la procédure associée
      $query = $ds->prepare("select * from procedures where CODEACTE = %", $this->code);
      $result = $ds->exec($query);
      if($ds->numRows($result) > 0) {
        $row = $ds->fetchArray($result);
        $this->procedure["code"] = $row["CODEPROCEDURE"];
        $query = $ds->prepare("select LIBELLELONG from actes where CODE = %", $this->procedure["code"]);
        $result = $ds->exec($query);
        $row = $ds->fetchArray($result);
        $this->procedure["texte"] = $row["LIBELLELONG"];
      } else {
        $this->procedure["code"] = "aucune";
        $this->procedure["texte"] = "";
      }
    }
  }
} 



?>