<?php

/**
 * dPccam
 *
 * Classe des informations sur l'acte CCAM
 *
 * @category Ccam
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * Class CCodeCCAM
 * Table p_acte
 *
 * Informations sur l'acte CCAM
 * Niveau acte
 */
class CCodeCCAM extends CCCAM {
  // Infos sur le code
  public $code;
  public $libelle_court;
  public $libelle_long;
  public $type_acte;
  public $_type_acte;
  public $sexe_comp;
  public $place_arbo;
  public $date_creation;
  public $date_fin;
  public $frais_dep;

  // Nature d'assurance permises
  public $assurance;
  // Classification du code dans l'arborescence
  public $arborescence;

  // Forfait spécifique permis par le code (table forfaits)
  public $_forfait;

  // Références

  // Infos historisées sur le code
  /** @var  CInfoTarifCCAM[] */
  public $_ref_infotarif;
  // Procédures historisées
  /** @var  CProcedureCCAM[] */
  public $_ref_procedures;
  // Notes
  /** @var  CNoteCCAM[] */
  public $_ref_notes;
  // Incompatibilités médicales
  /** @var  CIncompatibiliteCCAM[] */
  public $_ref_incompatibilites;
  // Activités
  /** @var  CActiviteCCAM[] */
  public $_ref_activites;

  // Elements de référence pour la récupération d'informations
  public $_activite;
  public $_phase;

  // Utilisation du cache
  static $cacheCount     = 0;
  static $useCount       = 0;
  static $cacheCountLite = 0;
  static $useCountLite   = 0;
  static $cache = array();
  static $cacheLite = array();
  /**
   * Constructeur à partir du code CCAM
   *
   * @param string $code Le code CCAM
   *
   * @return string|self
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
   * Chargement optimisé des codes CCAM
   *
   * @param string $code Code CCAM
   *
   * @return CCodeCCAM
   */
  static function get($code) {
    self::$useCount++;

    if (isset(self::$cache[$code])) {
      return self::$cache[$code];
    }

    if ($code_ccam = SHM::get("codeccam-$code")) {
      self::$cacheCount++;
      self::$cache[$code] = $code_ccam;
      return $code_ccam;
    }

    // Chargement
    $code_ccam = new CCodeCCAM($code);
    $code_ccam->load();
    SHM::put("codeccam-$code", $code_ccam, true);
    self::$cache[$code] = $code_ccam;
    return $code_ccam;
  }

  /**
   * Chargement des informations liées à l'acte
   * Table p_acte
   *
   * @return bool Existence ou pas du code CCAM
   */
  function load() {
    $ds = self::$spec->ds;

    $query = "SELECT p_acte.*
      FROM p_acte
      WHERE p_acte.CODE = %";
    $query = $ds->prepare($query, $this->code);
    $result = $ds->exec($query);
    if ($ds->numRows($result) == 0) {
      $this->code = "-";
      return false;
    }

    $row = $ds->fetchArray($result);
    $this->libelle_court  = $row["LIBELLECOURT"];
    $this->libelle_long   = $row["LIBELLELONG"];
    $this->type_acte      = $row["TYPE"];
    $this->sexe_comp      = $row["SEXE"];
    $this->place_arbo     = $row["PLACEARBORESCENCE"];
    $this->date_creation  = $row["DATECREATION"];
    $this->date_fin       = $row["DATEFIN"];
    $this->frais_dep      = $row["DEPLACEMENT"];

    $this->assurance = array();
    $this->assurance[1]["db"]  = $row["ASSURANCE1"];
    $this->assurance[2]["db"]  = $row["ASSURANCE2"];
    $this->assurance[3]["db"]  = $row["ASSURANCE3"];
    $this->assurance[4]["db"]  = $row["ASSURANCE4"];
    $this->assurance[5]["db"]  = $row["ASSURANCE5"];
    $this->assurance[6]["db"]  = $row["ASSURANCE6"];
    $this->assurance[7]["db"]  = $row["ASSURANCE7"];
    $this->assurance[8]["db"]  = $row["ASSURANCE8"];
    $this->assurance[9]["db"]  = $row["ASSURANCE9"];
    $this->assurance[10]["db"] = $row["ASSURANCE10"];

    $this->arborescence = array();
    $this->arborescence[1]["db"]  = $row["ARBORESCENCE1"];
    $this->arborescence[2]["db"]  = $row["ARBORESCENCE2"];
    $this->arborescence[3]["db"]  = $row["ARBORESCENCE3"];
    $this->arborescence[4]["db"]  = $row["ARBORESCENCE4"];
    $this->arborescence[5]["db"]  = $row["ARBORESCENCE5"];
    $this->arborescence[6]["db"]  = $row["ARBORESCENCE6"];
    $this->arborescence[7]["db"]  = $row["ARBORESCENCE7"];
    $this->arborescence[8]["db"]  = $row["ARBORESCENCE8"];
    $this->arborescence[9]["db"]  = $row["ARBORESCENCE9"];
    $this->arborescence[10]["db"] = $row["ARBORESCENCE10"];

    $this->loadTypeLibelle();
    $this->getForfaitSpec();
    $this->loadRefProcedures();
    $this->loadRefNotes();
    $this->loadRefIncompatibilites();

    $this->loadArborescence();
    $this->loadAssurance();
    $this->loadRefInfoTarif();
    foreach ($this->_ref_infotarif as $_info_tarif) {
      $_info_tarif->loadLibelleExo();
      $_info_tarif->loadLibellePresc();
      $_info_tarif->loadLibelleForfait();
    }
    $this->loadRefActivites();
    foreach ($this->_ref_activites as $_activite) {
      $_activite->loadLibelle();
      // Ne pas charger les associations possibles des codes complémentaires (des milliers)
      $_activite->_ref_associations = array();
      if ($this->type_acte != 2) {
        $_activite->loadRefAssociations();
      }

      $_activite->loadRefConvergence();
      $_activite->loadRefModificateurs();
      foreach ($_activite->_ref_modificateurs as $_date_modif) {
        foreach ($_date_modif as $_modif) {
          $_modif->loadLibelle();
        }
      }
      $_activite->loadRefClassif();
      foreach ($_activite->_ref_classif as $_classif) {
        $_classif->loadCatMed();
        $_classif->loadRegroupement();
      }
      $_activite->loadRefPhases();
      foreach ($_activite->_ref_phases as $_phase) {
        $_phase->loadRefInfo();
        $_phase->loadRefDentsIncomp();
        foreach ($_phase->_ref_dents_incomp as $_dent) {
          $_dent->loadRefDent();
          $_dent->_ref_dent->loadLibelle();
        }
      }
    }

    return true;
  }

  /**
   * Chargement des informations historisées de l'acte
   * Table p_acte_infotarif
   *
   * @return CInfoTarifCCAM[] La liste des informations historisées
   */
  function loadRefInfoTarif() {
    return $this->_ref_infotarif = CInfoTarifCCAM::loadListFromCode($this->code);
  }

  /**
   * Chargement des procédures de l'acte
   * Table p_acte_procedure
   *
   * @return CProcedureCCAM[] La liste des procédures
   */
  function loadRefProcedures() {
    return $this->_ref_procedures = CProcedureCCAM::loadListFromCode($this->code);
  }

  /**
   * Chargement des notes de l'acte
   * Table p_acte_notes
   *
   * @return CNoteCCAM[] La liste des notes
   */
  function loadRefNotes() {
    return $this->_ref_notes = CNoteCCAM::loadListFromCode($this->code);
  }

  /**
   * Chargement des incompatibilités de l'acte
   * Table p_acte_incompatibilite
   *
   * @return CIncompatibiliteCCAM[] La liste des incompatibilités
   */
  function loadRefIncompatibilites() {
    return $this->_ref_incompatibilites = CIncompatibiliteCCAM::loadListFromCode($this->code);
  }

  /**
   * Chargement des activités de l'acte
   * Table p_activite
   *
   * @return CActiviteCCAM[] La liste des activités
   */
  function loadRefActivites() {
    $exclude = array();
    if ($this->arborescence[1]["db"] == "000018" && $this->arborescence[2]["db"] == "000001") {
      $exclude[] = "'1'";
    }
    return $this->_ref_activites = CActiviteCCAM::loadListFromCode($this->code, $exclude);
  }

  /**
   * Chargement du libellé du type
   * Table c_typeacte
   *
   * @return string Libellé du type
   */
  function loadTypeLibelle() {
    $ds = self::$spec->ds;
    $query = "SELECT *
      FROM c_typeacte
      WHERE c_typeacte.CODE = %";
    $query = $ds->prepare($query, $this->type_acte);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->_type_acte = $row["LIBELLE"];
  }

  /**
   * Récupération du type de forfait de l'acte
   * (forfait spéciaux des listes SEH)
   * Table forfaits
   *
   * @return string Le forfait
   */
  function getForfaitSpec() {
    $ds = self::$spec->ds;
    $query = "SELECT *
      FROM forfaits
      WHERE forfaits.CODE = %";
    $query = $ds->prepare($query, $this->code);
    $result = $ds->exec($query);
    $row = $ds->fetchArray($result);
    $this->_forfait = $row["forfait"];
  }

  /**
   * Chargement des libellés des assurances
   * Table c_natureassurance
   *
   * @return array Liste des assurances
   */
  function loadAssurance() {
    $ds = self::$spec->ds;
    foreach ($this->assurance as &$assurance) {
      if (!$assurance["db"]) {
        continue;
      }
      $query = "SELECT *
        FROM c_natureassurance
        WHERE c_natureassurance.CODE = %";
      $query = $ds->prepare($query, $assurance["db"]);
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);
      $assurance["libelle"] = $row["LIBELLE"];
    }
  }

  /**
   * Chargement des informations de l'arborescence du code
   * Table c_arborescence
   *
   * @return array Arborescence complète
   */
  function loadArborescence() {
    $ds = self::$spec->ds;
    $pere  = "000001";
    $track = "";
    foreach ($this->arborescence as &$chapitre) {
      $rang = $chapitre["db"];
      if ($rang == "00000") {
        break;
      }
      $query = "SELECT *
        FROM c_arborescence
        WHERE c_arborescence.CODEPERE = %1
          AND c_arborescence.RANG = %2";
      $query = $ds->prepare($query, $pere, $rang);
      $result = $ds->exec($query);
      $row = $ds->fetchArray($result);

      if (!substr($row["RANG"], -2)) {
        break;
      }
      $track .= substr($row["RANG"], -2) . ".";
      $chapitre["rang"] = $track;
      $chapitre["code"] = $row["CODEMENU"];
      $chapitre["nom"]  = $row["LIBELLE"];
      $chapitre["rq"]   = array();
      $queryNotes = "SELECT *
        FROM c_notesarborescence
        WHERE c_notesarborescence.CODEMENU = %";
      $queryNotes = $ds->prepare($queryNotes, $chapitre["code"]);
      $resultNotes = $ds->exec($queryNotes);
      while ($rowNotes = $ds->fetchArray($resultNotes)) {
        $chapitre["rq"][] = str_replace("¶", "\n", $rowNotes["TEXTE"]);
      }
      $pere = $chapitre["code"];
    }
    return $this->arborescence;
  }



  /**
   * Récupération des informations minimales d'un code
   * Non caché
   *
   * @param string $code Code CCAM
   *
   * @return array()
   */
  static function getCodeInfos($code) {
    self::$useCountLite++;

    if (isset(self::$cacheLite[$code])) {
      return self::$cacheLite[$code];
    }

    if ($code_ccam = SHM::get("codeccamlite-$code")) {
      self::$cacheCountLite++;
      self::$cacheLite[$code] = $code_ccam;
      return $code_ccam;
    }

    // Chargement
    $ds = self::$spec->ds;

    $query = "SELECT p_acte.CODE, p_acte.LIBELLELONG, p_acte.TYPE
        FROM p_acte
        WHERE p_acte.CODE = %";
    $query = $ds->prepare($query, $code);
    $result = $ds->exec($query);
    $code_ccam = $ds->fetchArray($result);
    SHM::put("codeccamlite-$code", $code_ccam);
    self::$cacheLite[$code] = $code_ccam;
    return $code_ccam;
  }

  /**
   * Récupération du forfait d'un modificateur
   *
   * @param string $modificateur Lettre clé du modificateur
   *
   * @return array forfait et coefficient
   */
  static function getForfait($modificateur) {
    $ds = self::$spec->ds;
    $query = "SELECT *
        FROM t_modificateurforfait
        WHERE CODE = %
          AND DATEFIN = '00000000'";
    $query = $ds->prepare($query, $modificateur);
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
  static function getCoeffAsso($code) {
    $ds = self::$spec->ds;

    if ($code == "X") {
      return 0;
    }

    if (!$code) {
      return 100;
    }

    $query = $ds->prepare(
      "SELECT * FROM t_association WHERE CODE = % AND DATEFIN = '00000000'",
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
  static function findCodes($code='', $keys='', $max_length = null, $where = null) {
    $ds = self::getSpec()->ds;

    $query = "SELECT CODE, LIBELLELONG
                FROM p_acte
                WHERE 1 ";

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
   * @param string $code Code de l'acte
   *
   * @return array Tableau des actes
   */
  static function getActeRadio($code) {
    $ds = self::$spec->ds;

    $query = "SELECT code
        FROM ccam_radio
        WHERE code_saisi LIKE '%$code%'";
    return $ds->loadResult($query);
  }
}
