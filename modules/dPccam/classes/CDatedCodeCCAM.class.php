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
class CDatedCodeCCAM {
  public $date;          // Date de référence
  public $_date;         // date au style CCAM
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
  public $_code7;        // Possibilité d'ajouter le modificateur 7 (0 : non, 1 : oui)
  public $_default;
  public $_sorted_tarif; // Phases classées par ordre de tarif brut
  public $occ;
  public $_count_activite;

  // Code CCAM de référence
  /** @var  CCodeCCAM */
  public $_ref_code_ccam;

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

  /**
   * Constructeur à partir du code CCAM
   *
   * @param string $code Le code CCAM
   * @param string $date Date de référence
   *
   * @return self
   */
  function __construct($code = null, $date = null) {
    if (!$code || strlen($code) > 7) {
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
    $this->date = CMbDT::date($date);

    return null;
  }

  static $cache_layers = Cache::OUTER;

  /**
   * Chargement optimisé des codes CCAM
   *
   * @param string $code Code CCAM
   * @param string $date Date de référence
   *
   * @return CDatedCodeCCAM
   */
  static function get($code, $date = null) {
    // Chargement en fonction de la configuration
    if (CAppUI::conf("ccam CCodeCCAM use_new_ccam_architecture") == "COldCodeCCAM") {
      return COldCodeCCAM::get($code);
    }

    // Cache by copy needed : OUTER
    $date = CMbDT::date($date);
    $cache = new Cache(__METHOD__, array($code, $date), self::$cache_layers);
    if ($cache->exists()) {
      return $cache->get();
    }

    $code_ccam = new CDatedCodeCCAM($code, $date);
    $code_ccam->load();

    return $cache->put($code_ccam, true);
  }

  /**
   * Chargement complet d'un code
   * en fonction du niveau de profondeur demandé
   *
   * @return bool
   */
  function load() {
    $this->_ref_code_ccam = CCodeCCAM::get($this->code);
    $this->_date = CMbDT::format($this->date, "%Y%m%d");

    if (!$this->getLibelles()) {
      return false;
    }
    $this->getTarification();
    $this->getForfaitSpec();

    $this->getChaps();
    $this->getRemarques();
    $this->getActivites();

    $this->getActesAsso();
    $this->getActesIncomp();
    $this->getProcedure();
    $this->getActivite7();

    return true;
  }

  function __sleep() {
    $vars = get_object_vars($this);
    unset($vars["_ref_code_ccam"]);
    return array_keys($vars);
  }

  function __wakeup() {
    $this->_ref_code_ccam = CCodeCCAM::get($this->code);
  }

  /**
   * Récuparation des libellés du code
   *
   * @return bool etat de validité de l'acte cherché
   */
  function getLibelles() {
    // Vérification que le code est actif à la date donnée
    if ($this->_ref_code_ccam->date_fin != "00000000" && $this->_ref_code_ccam->date_fin <= $this->_date) {
      $this->code = "-";
      //On rentre les champs de la table actes
      $this->libelleCourt = "Acte inconnu ou supprimé";
      $this->libelleLong = "Acte inconnu ou supprimé";
      $this->_code7 = 1;
      return false;
    }

    $this->libelleCourt = $this->_ref_code_ccam->libelle_court;
    $this->libelleLong  = $this->_ref_code_ccam->libelle_long;
    $this->type         = $this->_ref_code_ccam->type_acte;
    return true;
  }

  /**
   * Vérification de l'existence du moficiateur 7 pour l'acte
   *
   * @return void
   */
  function getActivite7() {
    $this->_code7 = 0;
    foreach ($this->activites as $activite) {
      foreach ($activite->modificateurs as $modificateur) {
        if ($modificateur->code == "7") {
          $this->_code7 = 1;
        }
      }
    }
  }

  /**
   * Récupération de la possibilité de remboursement de l'acte
   *
   * @return int l'admission au remboursement
   */
  function getTarification() {
    foreach ($this->_ref_code_ccam->_ref_infotarif as $dateeffet => $infotarif) {
      if ($this->_date >= $dateeffet) {
        $this->remboursement = $infotarif->admission_rbt;
        return $this->remboursement;
      }
    }
    return 0;
  }

  /**
   * Récupération du type de forfait de l'acte
   * (forfait spéciaux des listes SEH)
   *
   * @return void
   */
  function getForfaitSpec() {
    $this->forfait = $this->_ref_code_ccam->_forfait;
  }

  /**
   * Chargement des chapitres de l'acte
   *
   * @return void
   */
  function getChaps() {
    if ($this->place) {
      return;
    }
    $this->couleur = $this->_couleursChap[intval($this->_ref_code_ccam->arborescence[1]["db"])];
    $this->chapitres[0]["db"]   = $this->_ref_code_ccam->arborescence[1]["db"];
    $this->place = $this->chapitres[0]["rang"] = $this->_ref_code_ccam->arborescence[1]["rang"];
    $this->chapitres[0]["code"] = $this->_ref_code_ccam->arborescence[1]["code"];
    $this->chapitres[0]["nom"]  = $this->_ref_code_ccam->arborescence[1]["nom"];
    $this->chapitres[0]["rq"]   = $this->_ref_code_ccam->arborescence[1]["rq"];
    if (isset($this->_ref_code_ccam->arborescence[2]["rang"])) {
      $this->chapitres[1]["db"]   = $this->_ref_code_ccam->arborescence[2]["db"];
      $this->place = $this->chapitres[1]["rang"] = $this->_ref_code_ccam->arborescence[2]["rang"];
      $this->chapitres[1]["code"] = $this->_ref_code_ccam->arborescence[2]["code"];
      $this->chapitres[1]["nom"]  = $this->_ref_code_ccam->arborescence[2]["nom"];
      $this->chapitres[1]["rq"]   = $this->_ref_code_ccam->arborescence[2]["rq"];
    }
    if (isset($this->_ref_code_ccam->arborescence[3]["rang"])) {
      $this->chapitres[2]["db"]   = $this->_ref_code_ccam->arborescence[3]["db"];
      $this->place = $this->chapitres[2]["rang"] = $this->_ref_code_ccam->arborescence[3]["rang"];
      $this->chapitres[2]["code"] = $this->_ref_code_ccam->arborescence[3]["code"];
      $this->chapitres[2]["nom"]  = $this->_ref_code_ccam->arborescence[3]["nom"];
      $this->chapitres[2]["rq"]   = $this->_ref_code_ccam->arborescence[3]["rq"];
    }
    if (isset($this->_ref_code_ccam->arborescence[4]["rang"])) {
      $this->chapitres[3]["db"]   = $this->_ref_code_ccam->arborescence[4]["db"];
      $this->place = $this->chapitres[3]["rang"] = $this->_ref_code_ccam->arborescence[4]["rang"];
      $this->chapitres[3]["code"] = $this->_ref_code_ccam->arborescence[4]["code"];
      $this->chapitres[3]["nom"]  = $this->_ref_code_ccam->arborescence[4]["nom"];
      $this->chapitres[3]["rq"]   = $this->_ref_code_ccam->arborescence[4]["rq"];
    }
  }

  /**
   * Chargement des remarques sur l'acte
   *
   * @return void
   */
  function getRemarques() {
    $this->remarques = array();
    foreach ($this->_ref_code_ccam->_ref_notes as $note) {
      $this->remarques[] = str_replace("¶", "\n", $note->texte);
    }
  }

  /**
   * Chargement des activités de l'acte
   *
   * @return array La liste des activités
   */
  function getActivites() {
    $this->getChaps();
    foreach ($this->_ref_code_ccam->_ref_activites as $activite) {
      $datedActivite = new CObject();
      $datedActivite->numero  = $activite->code_activite;
      $datedActivite->type    = $activite->_libelle_activite;
      $datedActivite->libelle = "";
      // On ne met pas l'activité 1 pour les actes du chapitre 18.01
      if ($this->chapitres[0]["db"] != "000018" || $this->chapitres[1]["db"] != "000001" || $datedActivite->numero != "1") {
        $this->activites[$datedActivite->numero] = $datedActivite;
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
      $this->getPhasesFromActivite($activite);
      $this->getModificateursFromActivite($activite);
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
   * pour une phase d'une activité donnée
   *
   * @param object $activite Activité concernée
   * @param object $phase    Phase concernée
   *
   * @return object liste de modificateurs de convergence disponibles
   */
  function getConvergenceFromActivitePhase($activite, $phase) {
    return $this->_ref_code_ccam->_ref_activites[$activite->numero]->_ref_phases[$phase->phase]->_ref_convergence;
  }

  /**
   * Récupération des modificateurs d'une activité
   *
   * @param object &$activite Activité concernée
   *
   * @return void
   */
  function getModificateursFromActivite(&$activite) {
    $listModifConvergence = array("X", "I", "9", "O");
    // Extraction des modificateurs
    $activite->modificateurs = array();
    $modificateurs =& $activite->modificateurs;
    $listModificateurs = array();
    foreach ($this->_ref_code_ccam->_ref_activites[$activite->numero]->_ref_modificateurs as $dateEffet => $liste) {
      if ($dateEffet <= $this->_date) {
        $listModificateurs = $liste;
        break;
      }
    }
    $modifsConvergence = array();
    // Ajout des modificateurs normaux
    foreach ($listModificateurs as $modificateur) {
      /* Verification de la date de fin d'effet des modificateurs */
      if ($modificateur->date_fin != "00000000" && $this->_date > $modificateur->date_fin) {
        continue;
      }

      // Cas d'un modificateur de convergence
      $_modif = new CObject();
      $_modif->code    = $modificateur->modificateur;
      $_modif->libelle = $modificateur->_libelle;
      $_modif->_checked = null;
      $_modif->_state = null;
      if (in_array($modificateur->modificateur, $listModifConvergence)) {
        $modifsConvergence[] = $modificateur;
      }
      // Cas d'un modificateur normal
      else {
        $_modif->_double = "1";
        $modificateurs[] = $_modif;
      }
    }

    foreach ($activite->phases as &$_phase) {
      // Ajout des modificateurs pour les phases dont le tarif existe
      $_phase->_modificateurs = array();
      if ($_phase->tarif) {
        $_phase->_modificateurs = $activite->modificateurs;
        $convergence = $this->getConvergenceFromActivitePhase($activite, $_phase);

        if ($convergence) {
          $mod_convergence_text = '';
          foreach ($modifsConvergence as $_modif_convergence) {
            $mod_simple = 'mod' . $_modif_convergence->modificateur;
            $mod_double = 'mod' . $_modif_convergence->modificateur . $_modif_convergence->modificateur;

            if ($convergence->$mod_simple) {
              $mod_convergence_text .= $_modif_convergence->modificateur;
            }
            elseif ($convergence->$mod_double) {
              $mod_convergence_text .= $_modif_convergence->modificateur . $_modif_convergence->modificateur;
            }
          }

          if ($mod_convergence_text != '') {
            $_modif = new CObject();
            $_modif->code = $mod_convergence_text;
            $_modif->libelle = 'Modificateur transitoire de convergence vers la cible';
            $_modif->_checked = null;
            $_modif->_state = null;
            $_modif->_double = strlen($_modif->code);
            $_phase->_modificateurs[] = $_modif;
          }
        }
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
    $activite->phases = array();
    $phases =& $activite->phases;
    $infoPhase = null;
    foreach ($this->_ref_code_ccam->_ref_activites[$activite->numero]->_ref_phases as $phase) {
      foreach ($phase->_ref_classif as $dateEffet => $info) {
        if ($dateEffet <= $this->_date) {
          $infoPhase = $info;
          break;
        }
      }
      $datedPhase               = new CObject();
      $datedPhase->phase        = $phase->code_phase;
      $datedPhase->libelle      = "Phase Principale";
      $datedPhase->nb_dents     = intval($phase->nb_dents);
      $datedPhase->dents_incomp = $phase->_ref_dents_incomp;
      if ($infoPhase) {
        $datedPhase->tarif   = floatval($infoPhase->prix_unitaire)/100;
        $datedPhase->tarif2  = floatval($infoPhase->prix_unitaire2)/100;
        $datedPhase->charges = floatval($infoPhase->charge_cab)/100;
      }
      else {
        $datedPhase->tarif   = 0;
        $datedPhase->tarif2  = 0;
        $datedPhase->charges = 0;
      }
      // Ordre des tarifs décroissants pour l'activité 1
      if ($activite->numero == "1") {
        if ($datedPhase->tarif != 0) {
          $this->_sorted_tarif = 1 / $datedPhase->tarif;
        }
        else {
          $this->_sorted_tarif = 1;
        }
      }
      elseif ($this->_sorted_tarif === null) {
        $this->_sorted_tarif = 2;
      }

      // Ajout de la phase
      $phases[$phase->code_phase] = $datedPhase;
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
   * Récupération des codes associés d'une activité
   *
   * @param array  &$activite Activité concernée
   * @param string $code      Chaine de caractère à trouver dans les résultats
   * @param int    $limit     Nombre max de codes retournés
   *
   * @return void
   */
  function getAssoFromActivite(&$activite, $code = null, $limit = null) {
    // Extraction des phases
    $assos = array();
    $anesth_comp = '';
    if ($this->type == 2) {
      $activite->assos = $assos;
      $activite->anesth_comp = $anesth_comp;
      return;
    }
    $listeAsso = array();
    foreach ($this->_ref_code_ccam->_ref_activites[$activite->numero]->_ref_associations as $dateEffet => $liste) {
      if ($dateEffet <= $this->_date) {
        $listeAsso = $liste;
        break;
      }
    }
    /** @var $asso CActiviteAssociationCCAM */
    foreach ($listeAsso as $asso) {
      $assos[$asso->acte_asso]["code"]  = $asso->_ref_code["CODE"];
      $assos[$asso->acte_asso]["texte"] = $asso->_ref_code["LIBELLELONG"];
      $assos[$asso->acte_asso]["type"]  = $asso->_ref_code["TYPE"];

      /* Vérification si l'un des codes associés est une anesthésie complémentaire */
      if (in_array($asso->acte_asso, array('ZZLP008', 'ZZLP012', 'ZZLP025', 'ZZLP030', 'ZZLP042', 'ZZLP054' ))) {
        $anesth_comp = $asso->acte_asso;
      }
    }
    $this->assos = array_merge($this->assos, $assos);
    $activite->assos = $assos;
    $activite->anesth_comp = $anesth_comp;
  }

  /**
   * Récupération des actes associés (compléments / suppléments)
   *
   * @param string $code  Chaine de caractère à trouver dans les résultats
   * @param int    $limit Nombre max de codes retournés
   *
   * @return void
   */
  function getActesAsso($code = null, $limit = null) {
    foreach ($this->activites as &$activite) {
      $this->getAssoFromActivite($activite, $code, $limit);
    }
  }

  /**
   * Récupération de la liste des actes incompatibles à l'acte
   *
   * @return void
   */
  function getActesIncomp() {
    $incomps    = array();
    $listIncomp = array();
    foreach ($this->_ref_code_ccam->_ref_incompatibilites as $dateEffet => $liste) {
      if ($dateEffet <= $this->_date) {
        $listIncomp = $liste;
        break;
      }
    }
    /** @var $incomp CIncompatibiliteCCAM */
    foreach ($listIncomp as $incomp) {
      $incomps[$incomp->code_incomp]["code"]  = $incomp->_ref_code["CODE"];
      $incomps[$incomp->code_incomp]["texte"] = $incomp->_ref_code["LIBELLELONG"];
      $incomps[$incomp->code_incomp]["type"]  = $incomp->_ref_code["TYPE"];
    }

    $this->incomps = $incomps;
  }

  /**
   * Récupération de la première procédure liée à l'acte
   *
   * @return void
   */
  function getProcedure() {
    $listProc = array();
    foreach ($this->_ref_code_ccam->_ref_procedures as $dateEffet => $liste) {
      if ($dateEffet <= $this->_date) {
        $listProc = $liste;
        break;
      }
    }
    if (count($listProc)) {
      $procedure = reset($listProc);
      $this->procedure["code"]  = $procedure->_ref_code["CODE"];
      $this->procedure["texte"] = $procedure->_ref_code["LIBELLELONG"];
      $this->procedure["type"]  = $procedure->_ref_code["TYPE"];
    }
    else {
      $this->procedure["code"]  = "";
      $this->procedure["texte"] = "";
      $this->procedure["type"]  = "";
    }
  }

  /**
   * Récupération du forfait d'un modificateur
   *
   * @param string $modificateur Lettre clé du modificateur
   * @param string $date         Date de référence
   *
   * @return array forfait et coefficient
   */
  function getForfait($modificateur, $date = null) {
    return CCodeCCAM::getForfait($modificateur, $date);
  }

  /**
   * Récupération du coefficient d'association
   *
   * @param string $code Code d'association
   *
   * @return float
   */
  function getCoeffAsso($code) {
    return CCodeCCAM::getCoeffAsso($code);
  }

  /**
   * Check wether an acte is a complement or not
   *
   * @return bool
   */
  function isComplement() {
    $this->getChaps();
    return isset($this->chapitres[1]) && $this->chapitres[1]['rang'] == '18.02.';
  }

  /**
   * Check wether an acte is a supplement or not
   *
   * @return bool
   */
  function isSupplement() {
    $this->getChaps();
    return isset($this->chapitres[1]) && $this->chapitres[1]['rang'] == '19.02.';
  }

  /**
   * Check wether an acte is inclued in 'acte d'imagerie pour acte de radiologie interventionnelle
   * ou cardiologie interventionnelle'
   *
   * @return bool
   */
  function isRadioCardioInterv() {
    $this->getChaps();
    return isset($this->chapitres[3]) && $this->chapitres[3]['rang'] == '19.01.09.02.';
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
    return CCodeCCAM::findCodes($code, $keys, $max_length, $where);
  }

  /**
   * Récupération des actes radio
   *
   * @return array Tableau des actes
   */
  function getActeRadio() {
    return CCodeCCAM::getActeRadio($this->code);
  }

  /**
   * Chargement des actes voisins
   */
  function loadActesVoisins() {
    $query = "SELECT CODE
    FROM p_acte
    WHERE DATEFIN = '00000000' ";
    foreach ($this->chapitres as $_key => $_chapitre ) {
      $chapitre_db = $_chapitre["db"];
      switch ($_key) {
        case "0":
          $query .= " AND ARBORESCENCE1 = '$chapitre_db'";
          break;

        case "1":
          $query .= " AND ARBORESCENCE2 = '$chapitre_db'";
          break;

        case "2":
          $query .= " AND ARBORESCENCE3 = '$chapitre_db'";
          break;

        case "3":
          $query .= " AND ARBORESCENCE4 = '$chapitre_db'";
          break;

        default;
      }
    }
    $query .= " ORDER BY CODE LIMIT 0 , 100";
    $acte_voisins = array();

    $ds = CSQLDataSource::get("ccamV2");
    $result       = $ds->exec($query);
    while ($row = $ds->fetchArray($result)) {
      $acte_voisin = CDatedCodeCCAM::get($row["CODE"]);
      $acte_voisin->_ref_code_ccam->date_creation = preg_replace(
        '/^(\d{4})(\d{2})(\d{2})$/', '\\3/\\2/\\1', $acte_voisin->_ref_code_ccam->date_creation
      );
      $acte_voisins[] = $acte_voisin;
    }

    return $acte_voisins;
  }

  /**
   * Change date format yyyyddmm at yyyy/mm/dd
   *
   * @param string $dateFrom Date
   *
   * @return date format yyyy/mm/dd
   */
  static function mapDateFrom($dateFrom) {
    return preg_replace('/^(\d{4})(\d{2})(\d{2})$/', '\\3/\\2/\\1', $dateFrom);
  }

  /**
   * Change date format yyyy/mm/dd at yyyymmdd
   *
   * @param string $dateTo Date
   *
   * @return date format yyyymmdd
   */
  static function mapDateToSlash($dateTo) {
    $date = explode("/", $dateTo);
    return $date[2].$date[1].$date[0];
  }

  /**
   * Change date format yyyy-mm-dd at yyyymmdd
   *
   * @param string $dateTo Date
   *
   * @return date format yyyymmdd
   */
  static function mapDateToDash($dateTo) {
    $date = explode("-", $dateTo);
    return $date[0].$date[1].$date[2];
  }
}
