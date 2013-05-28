<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Groupement Homogène de Malade est la structure principale de facturation déterminée par le PMSI
 * En assure la persistence et les outils pour le produire
 */
class CGHM extends CMbObject {
  // DB Table key
  public $ghm_id;

  // DB fields
  /** @var ref|int Séjour associé */
  public $sejour_id;
  /** @var string Code CIM du diagnostic relié */
  public $DR;
  /** @var string Codes CIM sérialisés des diagnostics associés significatfifs retenus */
  public $DASs;
  /** @var string Codes CIM sérialisés des diagnostics associés documentaires retenus */
  public $DADs;

  // Derived fields
  /** @var string Age du patient */
  public $_age;
  /** @var string Sexe du patient */
  public $_sexe;
  /** @var string Diagnostic principal du séjour */
  public $_DP;
  /** @var string Motif d'hospitalisation */
  public $_motif;
  /** @var string Type d'hospitalisaton du séjour */
  public $_type_hospi;
  /** @var int Durée en nuits du séjours */
  public $_duree;
  /** @var int Nombre de séances ? */
  public $_seances;
  /** @var string Destination d'hospitalisation, eg MCO */
  public $_destination;

  // Diagnostics
  public $_DASs = array(); // Diagnostics associés significatifs
  public $_DADs = array(); // Diagnostics associés documentaires

  // Results
  public $_CM;
  public $_CM_nom;
  public $_GHM;
  public $_GHM_nom;
  public $_GHM_groupe;
  public $_GHS;
  public $_borne_basse;
  public $_borne_haute;
  public $_tarif_2006;
  public $_EXH;
  public $_chemin;
  public $_notes       = array();

  public $_dsghm; // Data source pour le groupage

  // Forward references
  /** @var CSejour */
  public $_ref_sejour;

  // Distant references
  /** @var CPatient */
  public $_ref_patient;
  /** @var CActeCCAM[] Actes CCAM provenant du séjour */
  public $_ref_actes_ccam;
  /** @var array[] Tableau d'actes CCAM applatis */
  public $_actes;


  // Chrono
  public $_chrono;

  /**
   * Standard constuctor
   */
  function __construct() {
    parent::__construct();
    
    // Connection à la base
    $this->_dsghm = CSQLDataSource::get("GHS1010");
    
    // Initialisation des variables
    $this->_type_hospi = "comp";
    $this->_chemin = "";
    $this->_chrono = new chronometer();
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec( ) {
    $spec = parent::getSpec();
    $spec->table = 'ghm';
    $spec->key   = 'ghm_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps( ) {
    $props = parent::getProps();
    $props["sejour_id"] = "ref notNull class|CSejour";
    $props["DR"] = "str maxLength|10";
    $props["DASs"] = "text";
    $props["DADs"] = "text";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields( ) {
    parent::updateFormFields();
    if ($this->ghm_id) {
      $this->bindInfos();
      $this->getGHM();
    }
  }

  /**
   * Chsarge le séjour associé
   *
   * @return CSejour
   */
  function loadRefSejour() {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    return $this->loadRefSejour()->getPerm($permType);
  }

  /**
   * Liaison des infos aux séjour
   *
   * @return void
   */
  function bindInfos() {
    $sejour = $this->_ref_sejour;
    // Diagnostics
    $sejour->loadRefDossierMedical();
    $this->_DASs = $sejour->loadDiagnosticsAssocies();

    // Actes CCAM
    $sejour->loadRefsActesCCAM();
    $this->_ref_actes_ccam = $sejour->_ref_actes_ccam;

    $sejour->loadRefsOperations();
    foreach ($sejour->_ref_operations as $_operation) {
      $_operation->loadRefsActesCCAM();
      $this->_ref_actes_ccam = array_merge($this->_ref_actes_ccam, $_operation->_ref_actes_ccam);
    }

    $this->_ref_patient = $sejour->loadRefPatient();

    // Infos patient
    $adm = $sejour->_entree;
    $anadm = substr($adm, 0, 4);
    $moisadm = substr($adm, 5, 2);
    $jouradm = substr($adm, 8, 2);
    
    $nais = $this->_ref_patient->naissance;
    $annais = substr($nais, 0, 4);
    $moisnais = substr($nais, 5, 2);
    $journais = substr($nais, 8, 2);
    
    $this->_age = $anadm-$annais;
    if ($moisadm < $moisnais) {
      $this->_age = $this->_age - 1;
    }

    if ($jouradm < $journais && $moisadm == $moisnais) {
      $this->_age = $this->_age-1;
    }
    $this->_age .= "a";

    $this->_sexe = $this->_ref_patient->sexe == "m" ?  "Masculin" : "Féminin";

    // Infos hospi
    $this->_type_hospi = $sejour->type;
    $this->_duree = $sejour->_duree;
    $this->_motif = "hospi";
    $this->_destination = "MCO";

    // Infos codage
    // DP
    if (strlen($sejour->DP) > 3) {
      $this->_DP = substr($sejour->DP, 0, 3).".".substr($sejour->DP, 3);
    }
    else {
      $this->_DP = $sejour->DP;
    }
    // CCAM
    $this->_actes = array();
    foreach ($this->_ref_actes_ccam as $acte) {
      $this->_actes[] = array(
        "code" => $acte->code_acte,
        "phase" => $acte->code_phase,
        "activite" => $acte->code_activite
      );
    }
  }

  /**
   * Vérifie l'apparetnance du GHM à une liste
   *
   * @param string $type  Type d'entité à tester: DP, DR, DAS ou Actes
   * @param string $liste Nom de la liste
   *
   * @return int
   */
  function isFromList($type, $liste) {
    $elements = array();
    $liste_ids = array();
    $column1 = null;
    $column2 = null;
    $cma = null;
    $cm = null;
    switch ($type) {
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
        foreach ($this->_actes as $acte) {
          if ($acte["activite"] == 1) {
            $elements[] = $acte["code"];
          }
        }
        break;
      default :
        return 0;
    }

    if (preg_match("`^[AD]-[[:alnum:]]+`", $liste)) {
      $column1 = "code";
      $column2 = "liste_id";
      $liste_ids[] = $liste;
    }
    elseif (preg_match("`^CMA([[:alpha:]]{0,3})`", $liste, $cma)) {
      $column1 = "cma".strtolower($cma[1])."_id";
      $table = "cma".strtolower($cma[1]);
      $liste_ids[] = "";
    }
    elseif (preg_match("`^CM([[:digit:]]{2})`", $liste, $cm)) {
      $column1 = "code";
      $column2 = "CM_id";
      $liste_ids[] = $cm[1];
    }
    else {
      $column1 = "code";
      $column2 = "liste_id";
      $sql = "SELECT liste_id FROM liste WHERE nom LIKE '%$liste%'";
      $result = $this->_dsghm->exec($sql);
      if ($this->_dsghm->numRows($result) == 0) {
        return 0;
      }
      while ($row = $this->_spec->ds->fetchArray($result)) {
        $liste_ids[] = $row["liste_id"];
      }
    }
    $n = 0;
    foreach ($elements as $element) {
      foreach ($liste_ids as $liste_id) {
        $sql = "SELECT * FROM $table WHERE $column1 = '$element'";
        if ($column2) {
          $sql .= "AND $column2 = '$liste_id'";
        }
        $result = $this->_dsghm->exec($sql);
        $n = $n + $this->_dsghm->numRows($result);
      }
    }
    return $n;
  }

  /**
   * Vérification de l'appartenance du GHM à un groupe (opératoire, médical, ...)
   *
   * @param string $type  Type d'entité à tester: DP, DR, DAS ou Actes
   * @param string $group Groupe à vérifier
   *
   * @return int
   */
  function isFromGroup($type, $group) {
    if($group == "non opératoires") {
      $n = 0;
      $sql = "SELECT * FROM liste WHERE nom LIKE '%(non opératoires)%'";
      $listeNO = $this->_dsghm->loadList($sql);
      foreach ($this->_actes as $acte) {
        $isNO = 0;
        foreach ($listeNO as $liste) {
          $sql = "SELECT code FROM acte" .
              "\nWHERE code = '".$acte["code"]."'";
          $resultExists = $this->_dsghm->exec($sql);
          $sql = "SELECT code FROM acte" .
              "\nWHERE code = '".$acte["code"]."'" .
              "\nAND phase = '".$acte["phase"]."'" .
              "\nAND liste_id = '".$liste["liste_id"]."'" .
              "\nAND CM_id = '$this->_CM'";
          $resultNO = $this->_dsghm->exec($sql);
          if (!$this->_dsghm->numRows($resultExists) || $this->_dsghm->numRows($resultNO)) {
            $isNO = 1;
          }
        }
        if ($isNO) {
          $n++;
        }
      }

      return ($n == count($this->_actes)) ? $n : 0;

    }
    elseif ($group == "operatoire") {
      $n = 0;
      $sql = "SELECT * FROM liste WHERE nom LIKE '%(non opératoires)%'";
      $listeNO = $this->_dsghm->loadList($sql);
      foreach ($this->_actes as $acte) {
        $isO = 1;
        foreach ($listeNO as $liste) {
          $sql = "SELECT code FROM acte" .
              "\nWHERE code = '".$acte["code"]."'" .
              "\nAND phase = '".$acte["phase"]."'" .
              "\nAND liste_id = '".$liste["liste_id"]."'" .
              "\nAND CM_id = '$this->_CM'";
          $result = $this->_dsghm->exec($sql);
          if ($this->_dsghm->numRows($result)) {
            $isO = 0;
          }
        }
        if ($isO) {
          $n++;
        }
      }
      return $n;
    }
    elseif ($group == "non médical") {
      $n = 0;
      foreach ($this->_actes as $acte) {
        $sql = "SELECT code FROM acte" .
            "\nWHERE code = '".$acte["code"]."'" .
            "\nAND phase = '".$acte["phase"]."'" .
            "\nAND liste_id = 'A-med'";
        $result = $this->_dsghm->exec($sql);
        if ($this->_dsghm->numRows($result)) {
          $n++;
        }
      }
      return $n;
    }
    elseif ($group == "activité 4") {
      $n = 0;
      foreach ($this->_actes as $acte) {
        if ($acte["activite"] == 4) {
          $n++;
        }
      }
      return $n;
    }

    return 0;
  }

  /**
   * Obtention de la catégorie majeure
   *
   * @return string|null Null si aucune catégorie majeure trouvé
   */
  function getCM() {
    // Vérification du type d'hospitalisation
    if ($this->_type_hospi == "séance") {
      $this->_CM = "28";
    }
    elseif ($this->_duree < 2) {
      $this->_CM = "24";
    }
    elseif ($this->isFromList("Actes", "transplantation")) {
      $this->_CM = "27";
    }
    elseif ($this->isFromList("DP", "D-039")) {
      $this->_CM = "26";
    }
    elseif (
      ($this->isFromList("DP", "D-036") && $this->isFromList("DAS", "D-037")) ||
      ($this->isFromList("DP", "D-037") && $this->isFromList("DAS", "D-036"))
    ) {
      $this->_CM = "25";
    }
    else {
      $sql = "SELECT * FROM diagcm WHERE diag = '$this->_DP'";
      $result = $this->_dsghm->exec($sql);
      if ($this->_dsghm->numRows($result) == 0) {
        $this->_CM = null;
      }
      else {
        $row = $this->_spec->ds->fetchArray($result);
        $this->_CM = $row["CM_id"];
      }
    }
    if ($this->_CM) {
      $sql = "SELECT * FROM cm WHERE CM_id = '$this->_CM'";
      $result = $this->_dsghm->exec($sql);
      $row = $this->_spec->ds->fetchArray($result);
      $this->_CM_nom = $row["nom"];
    }
    return $this->_CM;
  }
  
  /**
   * Vérification des conditions de l'arbre
   *
   * @param string $type Type
   * @param string $cond Condition
   *
   * @return int
   */
  function checkCondition($type, $cond) {
    $n = 0;
    $ageTest = null;
    $agePat  = null;
    $duree   = null;
    $seances = null;
    $this->_chemin .= "On teste ($type : $cond) -> ";
    if ($type == "1A" || $type == "2A" || $type == "nA") {
      if ($cond == "non opératoires" || $cond == "operatoire" || $cond == "non médical") {
        $n = $this->isFromGroup($type, $cond);
        if ($type[0] != "n") {
          $n = ($n >= $type[0]) ? 1 : 0;
        }
        else {
          $n = ($n == count($this->_actes)) ? 1 : 0;
        }
      }
      else {
        $n = $this->isFromList("Actes", $cond);
        if ($type[0] != "n") {
          $n = ($n >= $type[0]) ? 1 : 0;
        }
        else {
          $n = ($n == count($this->_actes)) ? 1 : 0;
        }
      }
    }
    elseif ($type == "DP") {
      $n = $this->isFromList("DP", $cond);
    }
    elseif ($type == "1DAS") {
      $n = $this->isFromList("DAS", $cond);
      if (!$n) {
        $this->_notes[] = "DAS en CMA possible ($cond)";
      }
    }
    elseif ($type == "DR") {
      $n = $this->isFromList("DR", $cond);
    }
    elseif ($type == "Age") {
      preg_match("`^([<>])([[:digit:]]+)([[:alpha:]])`", $cond, $ageTest);
      if (preg_match("`^([[:digit:]]+)([[:alpha:]])`", $this->_age, $agePat)) {
        if ($ageTest[1] == ">") {
          if ($ageTest[3] == "j" && $agePat[2] == "a") {
            $n = 1;
          }
          elseif ($ageTest[3] == $agePat[2] && $agePat[1] > $ageTest[2]) {
            $n = 1;
          }
        }
        elseif ($ageTest[1] == "<") {
          if ($ageTest[3] == "a" && $agePat[2] == "j") {
            $n = 1;
          }
          elseif ($ageTest[3] == $agePat[2] && $agePat[1] < $ageTest[2]) {
            $n = 1;
          }
        }
      }
    }
    elseif ($type == "Sexe") {
      if ($cond == $this->_sexe) {
        $n = 1;
      }
    }
    elseif ($type == "DS") {
      preg_match("`([<>=]{1,2})([[:digit:]]+)`", $cond, $duree);
      if ($duree[1] == ">=") {
        if ($this->_duree >= $duree[2]) {
          $n = 1;
        }
      }
      elseif ($duree[1] == "<") {
        if ($this->_duree < $duree[2]) {
          $n = 1;
        }
      }
    }
    elseif ($type == "NS") {
      preg_match("`([<>=]{1,2})([[:digit:]]+)`", $cond, $seances);
      if ($seances[1] == ">=") {
        if ($this->_seances >= $seances[2]) {
          $n = 1;
        }
      }
      elseif ($seances[1] == "<") {
        if ($this->_seances < $seances[2]) {
          $n = 1;
        }
      }
    }
    elseif ($type == "MS" && $cond == $this->_motif) {
      $n = 1;
    }
    elseif ($type == "Dest" && $cond == $this->_destination) {
      $n = 1;
    }
    $this->_chemin .= $n;
    return $n;
  }

  /**
   * Obtention du GHM
   *
   * @return void
   */
  function getGHM() {
    $this->_chrono->start();
    $this->_GHM = null;
    if (!$this->_DP) {
      $this->_GHM = "Diagnostic principal manquant";
      return;
    }
    foreach ($this->_DASs as $key => $DAS) {
      $sql = "SELECT * FROM incomp WHERE CIM1 = '$DAS' AND CIM2 = '".$this->_DP."'";
      $result = $this->_dsghm->exec($sql);
      if ($this->_dsghm->numRows($result)) {
        $this->_DADs[] = $DAS;
        unset($this->_DASs[$key]);
      }
    }
    if (!$this->_CM) {
      if (!$this->getCM()) {
        $this->_GHM = "Aucune catégorie majeur trouvée";
        return;
      }
    }
    $sql = "SELECT * FROM arbre WHERE CM_id = '$this->_CM'";
    $listeBranches = $this->_dsghm->loadList($sql);
    $parcoursBranches = 0;
    $row = $listeBranches[0];
    $maxcond = 5;
    for ($i = 1; ($i <= $maxcond*2) && ($this->_GHM === null); $i = $i + 2) {
      $type = $i;
      $cond = $i + 1;
      // On vérifie qu'on a pas déjà fait le test
      if (isset($oldrow) && $row != $oldrow) {
        while ($row[$type] == $oldrow[$type] && $row[$cond] == $oldrow[$cond]) {
          // On avance d'une ligne
          $parcoursBranches++;
          if (!isset($listeBranches[$parcoursBranches])) {
            if ($this->_CM != 1) {
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
      if ($row[$type] == '') {
        $this->_chemin .= "c'est bon";
        $this->_chemin .= " pour ".$row["GHM"]."\n";
        $this->_GHM = $row["GHM"];
      }
      elseif (!($this->checkCondition($row[$type], $row[$cond]))) {
        $this->_chemin .= " pour ".$row["GHM"]."\n";
        // On avance d'une ligne
        $parcoursBranches++;
        if (!isset($listeBranches[$parcoursBranches])) {
          if ($this->_CM != 1) {
            $this->_CM--;
            $this->_chrono->stop();
            $this->getGHM();
          }
          return;
        }
        $row = $listeBranches[$parcoursBranches];
        if (!$row[$type]) {
          $this->_GHM = $row["GHM"];
        }
        else {
          // On reviens à la dernière condition correcte
          $j = $i - 2; $nj = $j + 1;
          if ($j > 0) {
            while ($row[$j] != $oldrow[$j] && $row[$nj] != $oldrow[$nj] && $i > 1) {
              $i = $j; $j = $j - 2; $nj = $j + 1;
            }
          }
        }
        $i = $i - 2;
      }
      else {
        $this->_chemin .= " pour ".$row["GHM"]."\n";
      }
    }
    if ($this->_GHM) {
      $sql = "SELECT * FROM ghm WHERE GHM_id = '$this->_GHM'";
      $result = $this->_dsghm->exec($sql);
      $row = $this->_spec->ds->fetchArray($result);
      $this->_GHM_nom = $row["nom"];
      $this->_GHM_groupe = $row["groupe"];
      $this->_GHS = $row["GHS"];
      if ($this->_CM != "24") {
        $this->_borne_basse = $row["borne_basse"];
        $this->_borne_haute = $row["borne_haute"];
      }
      else {
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