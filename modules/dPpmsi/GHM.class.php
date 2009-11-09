<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author Romain Ollivier
 */

class CGHM extends CMbObject {
  // DB Table key
  var $ghm_id = null;
  
  // DB fields
  var $sejour_id    = null;
  var $DR           = null; // Diagnostic relié
  var $DASs         = null; // Diagnostics associés significatifs sérialisés
  var $DADs         = null; // Diagnostics associés documentaires sérialisés
  

  var $_dsghm = null; // Data source pour le groupage
  
  // Patient
  var $_age  = null;
  var $_sexe = null;
  
  // Actes
  var $_actes = null;
  
  // Hospi
  var $_type_hospi  = null;
  var $_duree       = null;
  var $_seances     = null;
  var $_motif       = null;
  var $_destination = null;
  
  // Diagnostics
  var $_DP   = null;    // Diagnostic principal
  var $_DASs = array(); // Diagnostics associés significatifs
  var $_DADs = array(); // Diagnostics associés documentaires
  
  // Results
  var $_CM          = null;
  var $_CM_nom      = null;
  var $_GHM         = null;
  var $_GHM_nom     = null;
  var $_GHM_groupe  = null;
  var $_GHS         = null;
  var $_borne_basse = null;
  var $_borne_haute = null;
  var $_tarif_2006  = null;
  var $_EXH         = null;
  var $_chemin      = null;
  var $_notes       = array();
  
  // Forward references
  var $_ref_sejour = null;

  // Chrono
  var $_chrono;

  // Constructeur
  function CGHM() {
    parent::__construct();
    
    // Connection à la base
    $this->_dsghm = CSQLDataSource::get("GHS1010");
    
    // Initialisation des variables
    $this->_type_hospi = "comp";
    $this->_chemin = "";
    $this->_chrono = new chronometer();
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ghm';
    $spec->key   = 'ghm_id';
    return $spec;
  }

  function getProps() {
  	$specsParent = parent::getProps();
    $specs = array (
      "sejour_id" => "ref notNull class|CSejour",
      "DR"        => "str maxLength|10",
      "DASs"      => "text",
      "DADs"      => "text"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if($this->ghm_id) {
      $this->loadRefsFwd();
      $this->bindInfos();
      $this->getGHM();
    }
  }
  
  function loadRefSejour() {
    $this->_ref_sejour = $this->loadFwdRef("sejour_id");
  }
    
  function getPerm($permType) {
    if(!$this->_ref_sejour) {
      $this->loadRefSejour();
    }
    return ($this->_ref_sejour->getPerm($permType));
  }
  
  // Liaison à un sejour
  function bindInfos() {
    // Diagnostics
    $this->_ref_sejour->loadRefDossierMedical();
    $this->_DASs = array(); 
    if ($this->_ref_sejour->_ref_dossier_medical->_id){
      foreach($this->_ref_sejour->_ref_dossier_medical->_codes_cim as $code) {
        if(strlen($code) < 4) {
          $this->_DASs[] = $code;
        } else {
          $this->_DASs[] = substr($code, 0, 3).".".substr($code, 3);
        }
      }
    }
    
    // Actes CCAM
    $this->_ref_sejour->loadRefsActesCCAM();
    $this->_ref_actes_ccam = $this->_ref_sejour->_ref_actes_ccam;

    $this->_ref_sejour->loadRefsOperations();
    foreach($this->_ref_sejour->_ref_operations as $_operation) {
      $_operation->loadRefsActesCCAM();
      $this->_ref_actes_ccam = array_merge($this->_ref_actes_ccam, $_operation->_ref_actes_ccam);
    }
    $this->_ref_patient =& $this->_ref_sejour->_ref_patient;

    // Infos patient
    $adm = $this->_ref_sejour->_entree;    
    $anadm = substr($adm, 0, 4);
    $moisadm = substr($adm, 5, 2);
    $jouradm = substr($adm, 8, 2);
    
    $nais = $this->_ref_patient->naissance;
    $annais = substr($nais, 0, 4);
    $moisnais = substr($nais, 5, 2);
    $journais = substr($nais, 8, 2);
    
    $this->_age = $anadm-$annais;
    if($moisadm<$moisnais){$this->_age=$this->_age-1;}
    if($jouradm<$journais && $moisadm==$moisnais){$this->_age=$this->_age-1;}
    $this->_age .= "a";
   
	  $this->_ref_patient->sexe == "m" ? $this->_sexe = "Masculin" : $this->_sexe = "Féminin";
    // Infos hospi
    $this->_type_hospi = $this->_ref_sejour->type;
    $this->_duree = $this->_ref_sejour->_duree;
    $this->_motif = "hospi";
    $this->_destination = "MCO";
    // Infos codage
    // DP
    if(strlen($this->_ref_sejour->DP) > 3) {
      $this->_DP = substr($this->_ref_sejour->DP, 0, 3).".".substr($this->_ref_sejour->DP, 3);
    }
    else {
      $this->_DP = $this->_ref_sejour->DP;
    }
    // CCAM
    $this->_actes = array();
    foreach($this->_ref_actes_ccam as $acte) {
      $this->_actes[] = array(
        "code" => $acte->code_acte,
        "phase" => $acte->code_phase,
        "activite" => $acte->code_activite
      );
    }
  }

  // Vérification de l'appartenance à une liste
  function isFromList($type, $liste) {
    $elements = array();
    $liste_ids = array();
    $column1 = null;
    $column2 = null;
    $cma = null;
    $cm = null;
    switch($type) {
      case "DP" :
        $table = "diag";
        $elements[] = $this->_DP;
        break;
      case "DR" :
        $table = "diag";
        $elements[] = $this->DR;
        break;
      case "DAS" :
        $table = "diag";
        $elements = $this->_DASs;
        break;
      case "Actes" :
        $table = "acte";
        foreach($this->_actes as $acte) {
          if($acte["activite"] == 1)
            $elements[] = $acte["code"];
        }
        break;
      default :
        return 0;
    }
    if(preg_match("`^[AD]-[[:alnum:]]+`", $liste)) {
      $column1 = "code";
      $column2 = "liste_id";
      $liste_ids[] = $liste;
    } else if (preg_match("`^CMA([[:alpha:]]{0,3})`", $liste, $cma)) {
      $column1 = "cma".strtolower($cma[1])."_id";
      $table = "cma".strtolower($cma[1]);
      $liste_ids[] = "";
    } else if(preg_match("`^CM([[:digit:]]{2})`", $liste, $cm)) {
      $column1 = "code";
      $column2 = "CM_id";
      $liste_ids[] = $cm[1];
    } else {
      $column1 = "code";
      $column2 = "liste_id";
      $sql = "SELECT liste_id FROM liste WHERE nom LIKE '%$liste%'";
      $result = $this->_dsghm->exec($sql);
      if(mysql_num_rows($result) == 0) {
        return 0;
      }
      while($row = $this->_spec->ds->fetchArray($result)) {
        $liste_ids[] = $row["liste_id"];
      }
    }
    $n = 0;
    foreach($elements as $element) {
      foreach($liste_ids as $liste_id) {
        $sql = "SELECT * FROM $table WHERE $column1 = '$element'";
        if($column2)
          $sql .= "AND $column2 = '$liste_id'";
        $result = $this->_dsghm->exec($sql);
        $n = $n + mysql_num_rows($result);
      }
    }
    return $n;
  }

  // Vérification de l'appartenance à un groupe (opératoire, médical, ...)
  function isFromGroup($type, $groupe) {
    if($groupe == "non opératoires") {
      $n = 0;
      $sql = "SELECT * FROM liste WHERE nom LIKE '%(non opératoires)%'";
      $listeNO = $this->_dsghm->loadList($sql);
      foreach($this->_actes as $acte) {
        $isNO = 0;
        foreach($listeNO as $liste) {
          $sql = "SELECT code FROM acte" .
              "\nWHERE code = '".$acte["code"]."'";
          $resultExists = $this->_dsghm->exec($sql);
          $sql = "SELECT code FROM acte" .
              "\nWHERE code = '".$acte["code"]."'" .
              "\nAND phase = '".$acte["phase"]."'" .
              "\nAND liste_id = '".$liste["liste_id"]."'" .
              "\nAND CM_id = '$this->_CM'";
          $resultNO = $this->_dsghm->exec($sql);
          if (!$this->_dsghm->numRows($resultExists) || $this->_dsghm->numRows($resultNO))
            $isNO = 1;
        }
        if($isNO)
          $n++;
      }
      if($n == count($this->_actes))
        return $n;
      else
        return 0;
    } else if($groupe == "operatoire") {
      $n = 0;
      $sql = "SELECT * FROM liste WHERE nom LIKE '%(non opératoires)%'";
      $listeNO = $this->_dsghm->loadList($sql);
      foreach($this->_actes as $acte) {
        $isO = 1;
        foreach($listeNO as $liste) {
          $sql = "SELECT code FROM acte" .
              "\nWHERE code = '".$acte["code"]."'" .
              "\nAND phase = '".$acte["phase"]."'" .
              "\nAND liste_id = '".$liste["liste_id"]."'" .
              "\nAND CM_id = '$this->_CM'";
          $result = $this->_dsghm->exec($sql);
          if ($this->_dsghm->numRows($result))
            $isO = 0;
        }
        if($isO)
          $n++;
      }
      return $n;
    } else if($groupe == "non médical") {
      $n = 0;
      foreach($this->_actes as $acte) {
        $sql = "SELECT code FROM acte" .
            "\nWHERE code = '".$acte["code"]."'" .
            "\nAND phase = '".$acte["phase"]."'" .
            "\nAND liste_id = 'A-med'";
        $result = $this->_dsghm->exec($sql);
        if ($this->_dsghm->numRows($result))
          $n++;
      }
      return $n;
    } else if($groupe == "activité 4") {
      $n = 0;
      foreach($this->_actes as $acte) {
        if($acte["activite"] == 4) {
          $n++;
        }
      }
      return $n;
    }
  }

  // Obtention de la catégorie majeure
  function getCM() {
    // Vérification du type d'hospitalisation
    if($this->_type_hospi == "séance") {
      $this->_CM = "28";
    //} else if($this->_type_hospi == "ambu" || $this->_type_hospi == "exte") {
    } else if($this->_duree < 2) {
      $this->_CM = "24";
    } else if($this->isFromList("Actes", "transplantation")) {
      $this->_CM = "27";
    } else if($this->isFromList("DP", "D-039")) {
      $this->_CM = "26";
    } else if(($this->isFromList("DP", "D-036") && $this->isFromList("DAS", "D-037"))||
              ($this->isFromList("DP", "D-037") && $this->isFromList("DAS", "D-036"))) {
      $this->_CM = "25";
    } else {
      $sql = "SELECT * FROM diagcm WHERE diag = '$this->_DP'";
      $result = $this->_dsghm->exec($sql);
      if($this->_dsghm->numRows($result) == 0) {
        $this->_CM = null;
      } else {
        $row = $this->_spec->ds->fetchArray($result);
        $this->_CM = $row["CM_id"];
      }
    }
    if($this->_CM) {
      $sql = "SELECT * FROM cm WHERE CM_id = '$this->_CM'";
      $result = $this->_dsghm->exec($sql);
      $row = $this->_spec->ds->fetchArray($result);
      $this->_CM_nom = $row["nom"];
    }
    return $this->_CM;
  }
  
  // Vérification des conditions de l'arbre
  function checkCondition($type, $cond) {
    $n = 0;
    $ageTest = null;
    $agePat  = null;
    $duree   = null;
    $seances = null;
    $this->_chemin .= "On teste ($type : $cond) -> ";
    if($type == "1A" || $type == "2A" || $type == "nA") {
      if($cond == "non opératoires" || $cond == "operatoire" || $cond == "non médical") {
        $n = $this->isFromGroup($type, $cond);
        if($type[0] != "n") {
          if($n >= $type[0]) {
            $n = 1;
          } else {
            $n = 0;
          }
        } else {
          if($n == count($this->_actes)) {
            $n = 1;
          } else {
            $n = 0;
          }
        }
      } else {
        $n = $this->isFromList("Actes", $cond);
        if($type[0] != "n") {
          if($n >= $type[0]) {
            $n = 1;
          } else {
            $n = 0;
          }
        } else {
          if($n == count($this->_actes)) {
            $n = 1;
          } else {
            $n = 0;
          }
        }
      }
    } else if($type == "DP") {
      $n = $this->isFromList("DP", $cond);
    } else if($type == "1DAS") {
      $n = $this->isFromList("DAS", $cond);
      if(!$n) {
        $this->_notes[] = "DAS en CMA possible ($cond)";
      }
     } else if($type == "DR") {
      $n = $this->isFromList("DR", $cond);
    } else if($type == "Age") {
      preg_match("`^([<>])([[:digit:]]+)([[:alpha:]])`", $cond, $ageTest);
      if(preg_match("`^([[:digit:]]+)([[:alpha:]])`", $this->_age, $agePat)) {
        if($ageTest[1] == ">") {
          if($ageTest[3] == "j" && $agePat[2] == "a") {
            $n = 1;
          } else if($ageTest[3] == $agePat[2] && $agePat[1] > $ageTest[2]) {
            $n = 1;
          }
        } else if($ageTest[1] == "<") {
          if($ageTest[3] == "a" && $agePat[2] == "j") {
            $n = 1;
          } else if($ageTest[3] == $agePat[2] && $agePat[1] < $ageTest[2]) {
            $n = 1;
          }
        }
      }
    } else if($type == "Sexe") {
      if($cond == $this->_sexe)
        $n = 1;
    } else if($type == "DS") {
      preg_match("`([<>=]{1,2})([[:digit:]]+)`", $cond, $duree);
      if($duree[1] == ">=") {
        if($this->_duree >= $duree[2]) {
          $n = 1;
        }
      } else if($duree[1] == "<") {
        if($this->_duree < $duree[2]) {
          $n = 1;
        }
      }
    } else if($type == "NS") {
      preg_match("`([<>=]{1,2})([[:digit:]]+)`", $cond, $seances);
      if($seances[1] == ">=") {
        if($this->_seances >= $seances[2]) {
          $n = 1;
        }
      } else if($seances[1] == "<") {
        if($this->_seances < $seances[2]) {
          $n = 1;
        }
      }
    } else if($type == "MS" && $cond == $this->_motif) {
      $n = 1;
    } else if($type == "Dest" && $cond == $this->_destination) {
      $n = 1;
    }
    $this->_chemin .= $n;
    return $n;
  }

  // Obtention du GHM
  function getGHM() {
    $this->_chrono->start();
    $this->_GHM = null;
    if(!$this->_DP) {
      $this->_GHM = "Diagnostic principal manquant";
      return;
    }
    foreach($this->_DASs as $key => $DAS) {
      $sql = "SELECT * FROM incomp WHERE CIM1 = '$DAS' AND CIM2 = '".$this->_DP."'";
      $result = $this->_dsghm->exec($sql);
      if($this->_dsghm->numRows($result)) {
        $this->_DADs[] = $DAS;
        unset($this->_DASs[$key]);
      }
    }
    if(!$this->_CM) {
      if(!$this->getCM()){
        $this->_GHM = "Aucune catégorie majeur trouvée";
        return;
      }
    }
    $sql = "SELECT * FROM arbre WHERE CM_id = '$this->_CM'";
    $listeBranches = $this->_dsghm->loadList($sql);
    $parcoursBranches = 0;
    $row = $listeBranches[0];
    $maxcond = 5;
    for($i = 1; ($i <= $maxcond*2) && ($this->_GHM === null); $i = $i + 2) {
      $type = $i;
      $cond = $i + 1;
      // On vérifie qu'on a pas déjà fait le test
      if(isset($oldrow) && $row != $oldrow) {
        while($row[$type] == $oldrow[$type] && $row[$cond] == $oldrow[$cond]) {
          // On avance d'une ligne
          $parcoursBranches++;
          if(!isset($listeBranches[$parcoursBranches])) {
            if($this->_CM != 1) {
              $this->_CM--;
              $this->_chrono->stop();
              $this->getGHM();
            }
            return;
          }
          $row = $listeBranches[$parcoursBranches];
        }
      }
      $oldrow = $row;
      $this->_chemin .= "Pour i = ".(($i+1)/2).", arbre_id = ".$row["arbre_id"].", ";
      if($row[$type] == '') {
        $this->_chemin .= "c'est bon";
        $this->_chemin .= " pour ".$row["GHM"]."\n";
        $this->_GHM = $row["GHM"];
      } else if(!($this->checkCondition($row[$type], $row[$cond]))) {
        $this->_chemin .= " pour ".$row["GHM"]."\n";
        // On avance d'une ligne
        $parcoursBranches++;
        if(!isset($listeBranches[$parcoursBranches])) {
          if($this->_CM != 1) {
            $this->_CM--;
            $this->_chrono->stop();
            $this->getGHM();
          }
          return;
        }
        $row = $listeBranches[$parcoursBranches];
        if(!$row[$type]) {
          $this->_GHM = $row["GHM"];
        } else {
          // On reviens à la dernière condition correcte
          $j = $i - 2; $nj = $j + 1;
          if($j > 0) {
            while($row[$j] != $oldrow[$j] && $row[$nj] != $oldrow[$nj] && $i > 1) {
              $i = $j; $j = $j - 2; $nj = $j + 1;
            }
          }
        }
        $i = $i - 2;
      } else {
        $this->_chemin .= " pour ".$row["GHM"]."\n";
      }
    }
    if($this->_GHM) {
      $sql = "SELECT * FROM ghm WHERE GHM_id = '$this->_GHM'";
      $result = $this->_dsghm->exec($sql);
      $row = $this->_spec->ds->fetchArray($result);
      $this->_GHM_nom = $row["nom"];
      $this->_GHM_groupe = $row["groupe"];
      $this->_GHS = $row["GHS"];
      if($this->_CM != "24") {
        $this->_borne_basse = $row["borne_basse"];
        $this->_borne_haute = $row["borne_haute"];
      } else {
        $this->_borne_basse = 0;
        $this->_borne_haute = 2;
      }
      $this->_tarif_2006 = $row["tarif_2006"];
      $this->_EXH = $row["EXH"];
    }
    $this->_chrono->stop();
    $this->_chemin .= "Calculé en ".$this->_chrono->total." secondes";
    return;
  }
}