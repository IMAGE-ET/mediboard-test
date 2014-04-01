<?php

/**
 * dPccam
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Classe pour gérer le mapping avec la base de données CCAM
 */
class CCodeCCAM {
  public $code;          // Code de l'acte
  public $chapitres;     // Chapitres de la CCAM concernes
  public $libelleCourt;  // Libelles
  public $libelleLong;
  public $place;         // Place dans la CCAM
  public $remarques;     // Remarques sur le code
  public $type;          // Type d'acte (isolé, procédure ou complément)
  public $activites = array(); // Activites correspondantes
  public $phases    = array(); // Nombre de phases par activités
  public $incomps   = array(); // Incompatibilite
  public $assos     = array(); // Associabilite
  public $procedure;     // Procedure
  public $remboursement; // Remboursement
  public $forfait;       // Forfait spécifique (SEH1, SEH2, SEH3, SEH4)
  public $couleur;       // Couleur du code par rapport à son chapitre

  // Variable calculées
  public $_code7; // Possibilité d'ajouter le modificateur 7 (0 : non, 1 : oui)
  public $_default;
  public $occ;

  // Distant field
  public $class;
  public $favoris_id;
  public $_ref_favori;

  // Activités et phases recuperées depuis le code CCAM
  public $_activite;
  public $_phase;

  /** @var CMbObjectSpec */
  public $_spec;

  public $_couleursChap = array(
    0  => "ffffff",
    1  => "669966",
    2  => "6666cc",
    3  => "6699ee",
    4  => "cc6633",
    5  => "ee6699",
    6  => "ff66ee",
    7  => "33cc33",
    8  => "66cc99",
    9  => "99ccee",
    10 => "cccc33",
    11 => "eecc99",
    12 => "ffccee",
    13 => "33ff33",
    14 => "66ff99",
    15 => "99ffee",
    16 => "ccff33",
    17 => "eeff99",
    18 => "ffffee",
    19 => "cccccc",
  );

  // niveaux de chargement
  const LITE   = 1;
  const MEDIUM = 2;
  const FULL   = 3;

  static $cacheCount = 0;

  static $useCount = array(
    self::LITE   => 0,
    self::MEDIUM => 0,
    self::FULL   => 0,
  );

  /** @var CMbObjectSpec */
  static $spec = null;

  /**
   * Get object spec
   *
   * @return CMbObjectSpec
   */
  static function getSpec() {
    if (self::$spec) {
      return self::$spec;
    }

    $spec = new CMbObjectSpec();
    $spec->dsn = "ccamV2";
    $spec->init();

    return self::$spec = $spec;
  }

  /**
   * Constructeur à partir du code CCAM
   *
   * @param string $code Le code CCAM
   *
   * @return self
   */
  function __construct($code = null) {
    $this->_spec = self::getSpec();

    if (strlen($code) > 7) {
      if (!preg_match("/^[A-Z]{4}[0-9]{3}(-[0-9](-[0-9])?)?$/i", $code)) {
         return "Le code $code n'est pas formaté correctement";
      }

      // Cas ou l'activite et la phase sont indiquées dans le code (ex: BFGA004-1-0)
      $detailCode = explode("-", $code);
      $this->code = strtoupper($detailCode[0]);
      $this->_activite = $detailCode[1];
      if (count($detailCode) > 2) {
        $this->_phase = $detailCode[2];
      }
    }
    else {
      $this->code = strtoupper($code);
    }
    return null;
  }

  /**
   * Methode de pré-serialisation
   *
   * @return array
   */
  function __sleep() {
    $fields = get_object_vars($this);
    unset($fields["_spec"]);
    return array_keys($fields);
  }

  /**
   * Méthode de "reveil" après serialisation
   *
   * @return void
   */
  function __wakeup() {
    $this->_spec = self::getSpec();
  }

  /**
   * Chargement optimisé des codes CCAM
   *
   * @param string $code Code CCAM
   * @param int    $niv  Niveau du chargement
   *
   * @return CCodeCCAM
   */
  static function get($code, $niv = self::MEDIUM) {
    self::$useCount[$niv]++;
    if ($code_ccam = SHM::get("code_ccam-$code-$niv")) {
      self::$cacheCount++;
      return $code_ccam;
    }

    // Chargement
    $code_ccam = new CCodeCCAM($code);
    $code_ccam->load($niv);

    SHM::put("code_ccam-$code-$niv", $code_ccam);

    return $code_ccam;
  }

  /**
   * Chargement complet d'un code
   * en fonction du niveau de profondeur demandé
   *
   * @param int $niv Niveau de profondeur demandé
   *
   * @return void
   */
  function load($niv) {
    if (!$this->getLibelles()) {
      return;
    }

    if ($niv == self::LITE) {
      $this->getActivite7();
    }

    if ($niv >= self::LITE) {
      $this->getTarification();
      $this->getForfaitSpec();
    }

    if ($niv >= self::MEDIUM) {
      $this->getChaps();
      $this->getRemarques();
      $this->getActivites();
    }

    if ($niv == self::FULL) {
      $this->getActesAsso();
      $this->getActesIncomp();
      $this->getProcedure();
    }
  }

  /**
   * Récuparation des libellés du code
   *
   * @return bool
   */
  function getLibelles() {
    $ds = $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $this->code);
    $result = $ds->exec($query);
    if ($ds->numRows($result) == 0) {
      $this->code = "-";
      //On rentre les champs de la table actes
      $this->libelleCourt = "Acte inconnu ou supprimé";
      $this->libelleLong = "Acte inconnu ou supprimé";
      $this->_code7 = 1;
      return false;
    }

    $row = $ds->fetchArray($result);
    //On rentre les champs de la table actes
    $this->libelleCourt = $row["LIBELLECOURT"];
    $this->libelleLong = $row["LIBELLELONG"];
    $this->type        = $row["TYPE"];
    return true;
  }

  /**
   * Vérification de l'existence du moficiateur 7 pour l'acte
   *
   * @return void
   */
  function getActivite7() {
    $ds = $this->_spec->ds;
    // recherche de la dernière date d'effet
    $query1 = "SELECT MAX(DATEEFFET) as LASTDATE FROM modificateuracte WHERE ";
    $query1 .= $ds->prepare("CODEACTE = %", $this->code);
    $query1 .= " GROUP BY CODEACTE";
    $result1 = $ds->exec($query1);
    // Chargement des modificateurs
    if ($ds->numRows($result1)) {
      $row = $ds->fetchArray($result1);
      $lastDate = $row["LASTDATE"];
      $query2 = "SELECT * FROM modificateuracte WHERE ";
      $query2 .= $ds->prepare("CODEACTE = %", $this->code);
      $query2 .= " AND CODEACTIVITE = '4'";
      $query2 .= " AND MODIFICATEUR = '7'";
      $query2 .= " AND DATEEFFET = '$lastDate'";
      $result2 = $ds->exec($query2);
      $this->_code7 = $ds->numRows($result2);
    }
    else {
      $this->_code7 = 1;
    }
  }

  /**
   * Récupération de la possibilité de remboursement de l'acte
   *
   * @return void
   */
  function getTarification() {
    $ds = $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM infotarif WHERE CODEACTE = % ORDER BY DATEEFFET DESC", $this->code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->remboursement = $row["REMBOURSEMENT"];
  }

  /**
   * Récupération du type de forfait de l'acte
   * (forfait spéciaux des listes SEH)
   *
   * @return void
   */
  function getForfaitSpec() {
    $ds = $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM forfaits WHERE CODE = %", $this->code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->forfait = $row["forfait"];
  }

  /**
   * Chargement des chapitres de l'acte
   *
   * @return void
   */
  function getChaps() {
    $ds = $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $this->code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);

    // On rentre les champs de la table actes
    $this->couleur = $this->_couleursChap[intval($row["ARBORESCENCE1"])];
    $this->chapitres[0]["db"] = $row["ARBORESCENCE1"];
    $this->chapitres[1]["db"] = $row["ARBORESCENCE2"];
    $this->chapitres[2]["db"] = $row["ARBORESCENCE3"];
    $this->chapitres[3]["db"] = $row["ARBORESCENCE4"];
    $pere = "000001";
    $track = "";

    // On rentre les infos sur les chapitres
    foreach ($this->chapitres as $key => $value) {
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
      while ($row2 = $ds->fetchArray($result2)) {
        $this->chapitres[$key]["rq"] .= "* " . str_replace("¶", "\n", $row2["TEXTE"]) . "\n";
      }
      $pere = $this->chapitres[$key]["code"];
    }
    $this->place = $this->chapitres[3]["rang"];
  }

  /**
   * Chargement des remarques sur l'acte
   *
   * @return void
   */
  function getRemarques() {
    $ds = $this->_spec->ds;
    $this->remarques = array();
    $query = $ds->prepare("SELECT * FROM notes WHERE CODEACTE = %", $this->code);
    $result = $ds->exec($query);
    while ($row = $ds->fetchArray($result)) {
      $this->remarques[] = str_replace("¶", "\n", $row["TEXTE"]);
    }
  }

  /**
   * Chargement des activités de l'acte
   *
   * @return array La liste des activités
   */
  function getActivites() {
    $this->getChaps();
    $ds = $this->_spec->ds;
    // Extraction des activités
    $query = "SELECT ACTIVITE AS numero
              FROM activiteacte
              WHERE CODEACTE = %";
    $query = $ds->prepare($query, $this->code);
    $result = $ds->exec($query);
    while ($obj = $ds->fetchObject($result)) {
      $obj->libelle = "";
      // On ne met pas l'activité 1 pour les actes du chapitre 18.01
      if ($this->chapitres[0]["db"] != "000018" || $this->chapitres[1]["db"] != "000001" || $obj->numero != "1") {
        $this->activites[$obj->numero] = $obj;
      }
    }
    // Libellés des activités
    foreach ($this->remarques as $remarque) {
      $match = null;
      if (preg_match("/Activité (\d) : (.*)/i", $remarque, $match)) {
        $this->activites[$match[1]]->libelle = $match[2];
      }
    }
    // Détail des activités
    foreach ($this->activites as &$activite) {
      // Type de l'activité
      $query = "SELECT LIBELLE AS `type`
                FROM activite
                WHERE CODE = %";
      $query = $ds->prepare($query, $activite->numero);
      $result = $ds->exec($query);
      $obj = $ds->fetchObject($result);
      $activite->type = $obj->type;
      // Modificateurs de l'activite
      $this->getModificateursFromActivite($activite);
      $this->getPhasesFromActivite($activite);
    }
    // Test de la présence d'activité virtuelle
    /**
    if (isset($this->activites[1]) && isset($this->activites[4])) {
      if (isset($this->activites[1]->phases[0]) && isset($this->activites[4]->phases[0])) {
        if ($this->activites[1]->phases[0]->tarif && !$this->activites[4]->phases[0]->tarif) {
          unset($this->activites[4]);
        }
        if (!$this->activites[1]->phases[0]->tarif && $this->activites[4]->phases[0]->tarif) {
          unset($this->activites[1]);
        }
      }
    }
    **/
    $this->_default = reset($this->activites);
    if (isset($this->_default->phases[0])) {
      $this->_default = $this->_default->phases[0]->tarif;
    }
    else {
      $this->_default = 0;
    }

    return $this->activites;
  }

  /**
   * Récupération des modificateurs de convergence
   * pour une activité donnée
   *
   * @param array $activite Activité concernée
   *
   * @return object liste de modificateurs de convergence disponibles
   */
  function getConvergenceFromActivite($activite) {
    $ds = $this->_spec->ds;
    // Recherche de la ligne des modificateurs de convergence
    $query = "SELECT *
              FROM convergence
              WHERE convergence.code = %1
                AND convergence.activite = %2";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);
    return $convergence = $ds->fetchObject($result);
  }

  /**
   * Récupération des modificateurs d'une activité
   *
   * @param array &$activite Activité concernée
   *
   * @return void
   */
  function getModificateursFromActivite(&$activite) {
    $convergence = $this->getConvergenceFromActivite($activite);
    $listModifConvergence = array("X", "I", "9", "O");
    $ds = $this->_spec->ds;
    // recherche de la dernière date d'effet
    $query = "SELECT MAX(DATEEFFET) AS LASTDATE
              FROM modificateuracte
              LEFT JOIN modificateurforfait
                ON modificateuracte.MODIFICATEUR = modificateurforfait.CODE
                AND modificateurforfait.DATEFIN = 00000000
              WHERE modificateuracte.CODEACTE = %1
                AND modificateurforfait.CODE IS NOT NULL
              GROUP BY modificateuracte.CODEACTE";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $lastDate = $row["LASTDATE"];
    // Extraction des modificateurs
    $activite->modificateurs = array();
    $modificateurs =& $activite->modificateurs;
    $query = "SELECT modificateuracte.MODIFICATEUR
              FROM modificateuracte
              WHERE modificateuracte.CODEACTE = %1
                AND modificateuracte.CODEACTIVITE = %2
                AND modificateuracte.DATEEFFET = '$lastDate'
              GROUP BY modificateuracte.MODIFICATEUR";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);

    while ($row = $ds->fetchArray($result)) {
      $query = "SELECT modificateur.CODE AS code, modificateur.LIBELLE AS libelle
                FROM modificateur
                WHERE CODE = %
                ORDER BY CODE";
      $query = $ds->prepare($query, $row["MODIFICATEUR"]);
      $_modif = $ds->fetchObject($ds->exec($query));

      // Cas d'un modificateur de convergence
      if (in_array($row["MODIFICATEUR"], $listModifConvergence)) {
        $simple = "mod".$row["MODIFICATEUR"];
        $double = "mod".$row["MODIFICATEUR"].$row["MODIFICATEUR"];
        if ($convergence->$simple) {
          $_modif->_double = "1";
          $modificateurs[] = $_modif;
        }
        if ($convergence->$double) {
          $_double_modif = clone $_modif;
          $_double_modif->_double = "2";
          $modificateurs[] = $_double_modif;
        }
      }
      // Cas d'un modificateur normal
      else {
        $_modif->_double = "1";
        $modificateurs[] = $_modif;
      }
    }
  }

  /**
   * Récupération des phases d'une activité
   *
   * @param array &$activite Activité concernée
   *
   * @return void
   */
  function getPhasesFromActivite(&$activite) {
    $ds = $this->_spec->ds;
    // Extraction des phases
    $activite->phases = array();
    $phases =& $activite->phases;
    $query = "SELECT phaseacte.PHASE AS phase,
                phaseacte.PRIXUNITAIRE AS tarif,
                phaseacte.CHARGESCAB charges
              FROM phaseacte
              WHERE phaseacte.CODEACTE = %1
                AND phaseacte.ACTIVITE = %2
              GROUP BY phaseacte.PHASE
              ORDER BY phaseacte.PHASE, phaseacte.DATE1 DESC";
    $query = $ds->prepare($query, $this->code, $activite->numero);
    $result = $ds->exec($query);

    while ($obj = $ds->fetchObject($result)) {
      $phases[$obj->phase] = $obj;
      $phase =& $phases[$obj->phase];
      $phase->tarif = floatval($obj->tarif)/100;
      $phase->libelle = "Phase Principale";
      $phase->charges = floatval($obj->charges)/100;

      // Copie des modificateurs pour chaque phase. Utile pour la salle d'op
      $phase->_modificateurs = $phase->tarif ? $activite->modificateurs : array();
    }

    // Libellés des phases
    foreach ($this->remarques as $remarque) {
      if (preg_match("/Phase (\d) : (.*)/i", $remarque, $match)) {
        if (isset($phases[$match[1]])) {
          $phases[$match[1]]->libelle = $match[2];
        }
      }
    }
  }

  /**
   * Récupération des actes associés (compléments / suppléments
   *
   * @param string $code  Chaine de caractère à trouver dans les résultats
   * @param int    $limit Nombre max de codes retournés
   *
   * @return void
   */
  function getActesAsso($code = null, $limit = null) {
    if ($this->type == 2) {
      return;
    }
    $ds = $this->_spec->ds;
    $queryEffet = $ds->prepare(
      "SELECT MAX(DATEEFFET) as LASTDATE FROM associabilite WHERE CODEACTE = % GROUP BY CODEACTE",
      $this->code
    );
    $resultEffet = $ds->exec($queryEffet);
    $rowEffet = $ds->fetchArray($resultEffet);
    $lastDate = $rowEffet["LASTDATE"];
    if ($code) {
      $code_explode = explode(" ", $code);
      $codeLike = array();
      foreach ($code_explode as $value) {
        $codeLike[] = "LIBELLELONG LIKE '%".addslashes($value) . "%'";
      }

      $query = "SELECT * FROM associabilite
        LEFT JOIN actes ON associabilite.ACTEASSO = actes.CODE
        WHERE CODEACTE = '$this->code'
        AND DATEEFFET = '$lastDate'
        AND (CODE LIKE '$code%'
          OR (".implode(" OR ", $codeLike)."))
        GROUP BY ACTEASSO";
    }
    else {
      $query = $ds->prepare(
        "SELECT * FROM associabilite WHERE CODEACTE = % AND DATEEFFET = '$lastDate' GROUP BY ACTEASSO",
        $this->code
      );
    }
    if ($limit) {
      $query .= " LIMIT $limit";
    }
    $result = $ds->exec($query);
    $i = 0;
    while ($row = $ds->fetchArray($result)) {
      $this->assos[$i]["code"] = $row["ACTEASSO"];
      $query2 = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", trim($row["ACTEASSO"]));
      $result2 = $ds->exec($query2);
      $row2 = $ds->fetchArray($result2);
      $this->assos[$i]["texte"] = $row2["LIBELLELONG"];
      $i++;
    }
  }

  /**
   * Récupération de la liste des actes incompatibles à l'acte
   *
   * @return void
   */
  function getActesIncomp() {
    $ds = $this->_spec->ds;
    $queryEffet = $ds->prepare(
      "SELECT MAX(DATEEFFET) as LASTDATE FROM incompatibilite WHERE CODEACTE = % GROUP BY CODEACTE",
      $this->code
    );
    $resultEffet = $ds->exec($queryEffet);
    $rowEffet = $ds->fetchArray($resultEffet);
    $lastDate = $rowEffet["LASTDATE"];
    $query = $ds->prepare(
      "SELECT * FROM incompatibilite WHERE CODEACTE = % AND DATEEFFET = '$lastDate' GROUP BY INCOMPATIBLE",
      $this->code
    );
    $result = $ds->exec($query);
    $i = 0;
    while ($row = $ds->fetchArray($result)) {
      $this->incomps[$i]["code"] = trim($row["INCOMPATIBLE"]);
      $query2 = $ds->prepare("SELECT * FROM actes WHERE CODE = % AND DATEFIN = '00000000'", trim($row["INCOMPATIBLE"]));
      $result2 = $ds->exec($query2);
      $row2 = $ds->fetchArray($result2);
      $this->incomps[$i]["texte"] = $row2["LIBELLELONG"];
      $i++;
    }
  }

  /**
   * Récupération de la procédure liée à l'acte
   *
   * @return void
   */
  function getProcedure() {
    $ds = $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM procedures WHERE CODEACTE = % GROUP BY CODEACTE ORDER BY DATEEFFET DESC", $this->code);
    $result = $ds->exec($query);
    if ($ds->numRows($result) > 0) {
      $row = $ds->fetchArray($result);
      $this->procedure["code"] = $row["CODEPROCEDURE"];
      $query2 = $ds->prepare("SELECT LIBELLELONG FROM actes WHERE CODE = % AND DATEFIN = '00000000'", $this->procedure["code"]);
      $result2 = $ds->exec($query2);
      $row2 = $ds->fetchArray($result2);
      $this->procedure["texte"] = $row2["LIBELLELONG"];
    }
    else {
      $this->procedure["code"] = "aucune";
      $this->procedure["texte"] = "";
    }
  }

  /**
   * Récupération du forfait d'un modificateur
   *
   * @param string $modificateur Lettre clé du modificateur
   *
   * @return array forfait et coefficient
   */
  function getForfait($modificateur) {
    $ds = $this->_spec->ds;
    $query = $ds->prepare("SELECT * FROM modificateurforfait WHERE CODE = % AND DATEFIN = '00000000'", $modificateur);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $valeur = array();
    $valeur["forfait"] = $row["FORFAIT"] / 100;
    $valeur["coefficient"] = $row["COEFFICIENT"] / 10;
    return $valeur;
  }

  /**
   * Récupération du coefficient d'association
   *
   * @param string $code Code d'association
   *
   * @return float
   */
  function getCoeffAsso($code) {
    if ($code == "X") {
      return 0;
    }

    if (!$code) {
      return 100;
    }

    $ds = $this->_spec->ds;
    $query = $ds->prepare(
      "SELECT * FROM association WHERE CODE = % AND DATEFIN = '00000000'",
      $code
    );
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $valeur = $row["COEFFICIENT"] / 10;
    return $valeur;
  }

  /**
   * Recherche de codes CCAM
   *
   * @param string $code       Codes partiels à chercher
   * @param string $keys       Mot clés à chercher
   * @param int    $max_length Longueur maximum du code
   * @param string $where      Autres paramètres where
   *
   * @return array Tableau d'actes
   */
  function findCodes($code='', $keys='', $max_length = null, $where = null) {
    $ds = $this->_spec->ds;

    $query = "SELECT CODE, LIBELLELONG
              FROM actes
              WHERE DATEFIN = '00000000' ";

    $keywords = explode(" ", $keys);
    $codes    = explode(" ", $code);
    CMbArray::removeValue("", $keywords);
    CMbArray::removeValue("", $codes);

    if ($keys != "") {
      $listLike = array();
      $codeLike = array();
      foreach ($keywords as $value) {
        $listLike[] = "LIBELLELONG LIKE '%".addslashes($value)."%'";
      }
      if ($code != "") {
        // Combiner la recherche de code et libellé
        foreach ($codes as $value) {
          $codeLike[] = "CODE LIKE '".addslashes($value) . "%'";
        }
        $query .= " AND ( (";
        $query .= implode(" OR ", $codeLike);
        $query .= ") OR (";
      }
      else {
        // Ou uniquement le libellé
        $query .= " AND (";
      }
      $query .= implode(" AND ", $listLike);
      if ($code != "") {
        $query .= ") ) ";
      }

    }
    if ($code && !$keys) {
      // Ou uniquement le code
      $codeLike = array();
      foreach ($codes as $value) {
        $codeLike[] = "CODE LIKE '".addslashes($value) . "%'";
      }
      $query .= "AND ". implode(" OR ", $codeLike);
    }

    if ($max_length) {
      $query .= " AND LENGTH(CODE) < $max_length ";
    }

    if ($where) {
      $query .= "AND " . $where;
    }

    $query .= " ORDER BY CODE LIMIT 0 , 100";

    $result = $ds->exec($query);
    $master = array();
    $i = 0;
    while ($row = $ds->fetchArray($result)) {
      $master[$i]["LIBELLELONG"] = $row["LIBELLELONG"];
      $master[$i]["CODE"] = $row["CODE"];
      $i++;
    }

    return($master);
  }

  /**
   * Récupération des actes radio
   *
   * @return array Tableau des actes
   */
  function getActeRadio() {
    $ds = $this->_spec->ds;
    $query = "SELECT code
      FROM ccam_radio
      WHERE code_saisi LIKE '%$this->code%'";
    return $ds->loadResult($query);
  }
}
