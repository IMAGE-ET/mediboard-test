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
 * Classe pour g�rer le mapping avec la base de donn�es CCAM
 */
class CDatedCodeCCAM {
  public $date;          // Date de r�f�rence
  public $_date;         // date au style CCAM
  public $code;          // Code de l'acte
  public $chapitres;     // Chapitres de la CCAM concernes
  public $libelleCourt;  // Libelles
  public $libelleLong;
  public $place;         // Place dans la CCAM
  public $remarques;     // Remarques sur le code
  public $type;          // Type d'acte (isol�, proc�dure ou compl�ment)
  public $activites = array(); // Activites correspondantes
  public $phases    = array(); // Nombre de phases par activit�s
  public $incomps   = array(); // Incompatibilite
  public $assos     = array(); // Associabilite
  public $procedure;     // Procedure
  public $remboursement; // Remboursement
  public $forfait;       // Forfait sp�cifique (SEH1, SEH2, SEH3, SEH4)
  public $couleur;       // Couleur du code par rapport � son chapitre

  // Variable calcul�es
  public $_code7;        // Possibilit� d'ajouter le modificateur 7 (0 : non, 1 : oui)
  public $_default;
  public $_sorted_tarif; // Phases class�es par ordre de tarif brut
  public $occ;

  // Code CCAM de r�f�rence
  /** @var  CCodeCCAM */
  public $_ref_code_ccam;

  // Distant field
  public $class;
  public $favoris_id;
  public $_ref_favori;

  // Activit�s et phases recuper�es depuis le code CCAM
  public $_activite;
  public $_phase;

  static $cache = array();

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
   * Constructeur � partir du code CCAM
   *
   * @param string $code Le code CCAM
   * @param string $date Date de r�f�rence
   *
   * @return self
   */
  function __construct($code = null, $date = null) {
    if (!$code || strlen($code) > 7) {
      if (!preg_match("/^[A-Z]{4}[0-9]{3}(-[0-9](-[0-9])?)?$/i", $code)) {
         return "Le code $code n'est pas format� correctement";
      }

      // Cas ou l'activite et la phase sont indiqu�es dans le code (ex: BFGA004-1-0)
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
    $this->date = $date ? $date : CMbDT::date();

    return null;
  }

  /**
   * Chargement optimis� des codes CCAM
   *
   * @param string $code Code CCAM
   * @param string $date Date de r�f�rence
   *
   * @return CDatedCodeCCAM
   */
  static function get($code, $date = null) {
    // Chargement en fonction de la configuration
    if (CAppUI::conf("ccam CCodeCCAM use_new_ccam_architecture") == "COldCodeCCAM") {
      return COldCodeCCAM::get($code);
    }

    $code_ccam = new CDatedCodeCCAM($code, $date);
    $code_ccam->load();

    return $code_ccam;
  }

  /**
   * Chargement complet d'un code
   * en fonction du niveau de profondeur demand�
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
   * R�cuparation des libell�s du code
   *
   * @return bool etat de validit� de l'acte cherch�
   */
  function getLibelles() {
    // V�rification que le code est actif � la date donn�e
    if ($this->_ref_code_ccam->date_fin != "00000000" && $this->_ref_code_ccam->date_fin <= $this->_date) {
      $this->code = "-";
      //On rentre les champs de la table actes
      $this->libelleCourt = "Acte inconnu ou supprim�";
      $this->libelleLong = "Acte inconnu ou supprim�";
      $this->_code7 = 1;
      return false;
    }

    $this->libelleCourt = $this->_ref_code_ccam->libelle_court;
    $this->libelleLong  = $this->_ref_code_ccam->libelle_long;
    $this->type         = $this->_ref_code_ccam->type_acte;
    return true;
  }

  /**
   * V�rification de l'existence du moficiateur 7 pour l'acte
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
   * R�cup�ration de la possibilit� de remboursement de l'acte
   *
   * @return int l'admission au remboursement
   */
  function getTarification() {
    foreach ($this->_ref_code_ccam->_ref_infotarif as $dateeffet => $infotarif) {
      if ($this->_date > $dateeffet) {
        $this->remboursement = $infotarif->admission_rbt;
        return $this->remboursement;
      }
    }
    return 0;
  }

  /**
   * R�cup�ration du type de forfait de l'acte
   * (forfait sp�ciaux des listes SEH)
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
      $this->remarques[] = str_replace("�", "\n", $note->texte);
    }
  }

  /**
   * Chargement des activit�s de l'acte
   *
   * @return array La liste des activit�s
   */
  function getActivites() {
    $this->getChaps();
    foreach ($this->_ref_code_ccam->_ref_activites as $activite) {
      $datedActivite = new CObject();
      $datedActivite->numero  = $activite->code_activite;
      $datedActivite->type    = $activite->_libelle_activite;
      $datedActivite->libelle = "";
      // On ne met pas l'activit� 1 pour les actes du chapitre 18.01
      if ($this->chapitres[0]["db"] != "000018" || $this->chapitres[1]["db"] != "000001" || $datedActivite->numero != "1") {
        $this->activites[$datedActivite->numero] = $datedActivite;
      }
    }
    // Libell�s des activit�s
    foreach ($this->remarques as $remarque) {
      $match = null;
      if (preg_match("/Activit� (\d) : (.*)/i", $remarque, $match)) {
        $this->activites[$match[1]]->libelle = $match[2];
      }
    }
    // D�tail des activit�s
    foreach ($this->activites as &$activite) {
      $this->getModificateursFromActivite($activite);
      $this->getPhasesFromActivite($activite);
    }
    // Test de la pr�sence d'activit� virtuelle
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
   * R�cup�ration des modificateurs de convergence
   * pour une activit� donn�e
   *
   * @param object $activite Activit� concern�e
   *
   * @return object liste de modificateurs de convergence disponibles
   */
  function getConvergenceFromActivite($activite) {
    return $this->_ref_code_ccam->_ref_activites[$activite->numero]->_ref_convergence;
  }

  /**
   * R�cup�ration des modificateurs d'une activit�
   *
   * @param object &$activite Activit� concern�e
   *
   * @return void
   */
  function getModificateursFromActivite(&$activite) {
    $convergence = $this->getConvergenceFromActivite($activite);
    $listModifConvergence = array("X", "I", "9", "O");
    // Extraction des modificateurs
    $activite->modificateurs = array();
    $modificateurs =& $activite->modificateurs;
    $listModificateurs = array();
    foreach ($this->_ref_code_ccam->_ref_activites[$activite->numero]->_ref_modificateurs as $dateEffet => $liste) {
      if ($dateEffet < $this->_date) {
        $listModificateurs = $liste;
        break;
      }
    }
    foreach ($listModificateurs as $modificateur) {
      // Cas d'un modificateur de convergence
      $_modif = new CObject();
      $_modif->code    = $modificateur->modificateur;
      $_modif->libelle = $modificateur->_libelle;
      $_modif->_checked = null;
      $_modif->_state = null;
      if (in_array($modificateur->modificateur, $listModifConvergence)) {
        $simple = "mod".$modificateur->modificateur;
        $double = "mod".$modificateur->modificateur.$modificateur->modificateur;
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
   * R�cup�ration des phases d'une activit�
   *
   * @param array &$activite Activit� concern�e
   *
   * @return void
   */
  function getPhasesFromActivite(&$activite) {
    $activite->phases = array();
    $phases =& $activite->phases;
    $infoPhase = null;
    foreach ($this->_ref_code_ccam->_ref_activites[$activite->numero]->_ref_phases as $phase) {
      foreach ($phase->_ref_classif as $dateEffet => $info) {
        if ($dateEffet < $this->_date) {
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
        $datedPhase->charges = floatval($infoPhase->charge_cab)/100;
      }
      else {
        $datedPhase->tarif   = 0;
        $datedPhase->charges = 0;
      }
      // Ordre des tarifs d�croissants pour l'activit� 1
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

      // Ajout des modificateurs pour les phases dont le tarif existe
      $datedPhase->_modificateurs = $datedPhase->tarif ? $activite->modificateurs : array();

      // Ajout de la phase
      $phases[$phase->code_phase] = $datedPhase;
    }

    // Libell�s des phases
    foreach ($this->remarques as $remarque) {
      if (preg_match("/Phase (\d) : (.*)/i", $remarque, $match)) {
        if (isset($phases[$match[1]])) {
          $phases[$match[1]]->libelle = $match[2];
        }
      }
    }
  }

  /**
   * R�cup�ration des codes associ�s d'une activit�
   *
   * @param array  &$activite Activit� concern�e
   * @param string $code      Chaine de caract�re � trouver dans les r�sultats
   * @param int    $limit     Nombre max de codes retourn�s
   *
   * @return void
   */
  function getAssoFromActivite(&$activite, $code = null, $limit = null) {
    // Extraction des phases
    $assos = array();
    if ($this->type == 2) {
      $activite->assos = $assos;
      return;
    }
    $listeAsso = array();
    foreach ($this->_ref_code_ccam->_ref_activites[$activite->numero]->_ref_associations as $dateEffet => $liste) {
      if ($dateEffet < $this->_date) {
        $listeAsso = $liste;
        break;
      }
    }
    /** @var $asso CActiviteAssociationCCAM */
    foreach ($listeAsso as $asso) {
      $assos[$asso->acte_asso]["code"]  = $asso->_ref_code["CODE"];
      $assos[$asso->acte_asso]["texte"] = $asso->_ref_code["LIBELLELONG"];
      $assos[$asso->acte_asso]["type"]  = $asso->_ref_code["TYPE"];
    }
    $this->assos = array_merge($this->assos, $assos);
    $activite->assos = $assos;
  }

  /**
   * R�cup�ration des actes associ�s (compl�ments / suppl�ments)
   *
   * @param string $code  Chaine de caract�re � trouver dans les r�sultats
   * @param int    $limit Nombre max de codes retourn�s
   *
   * @return void
   */
  function getActesAsso($code = null, $limit = null) {
    foreach ($this->activites as &$activite) {
      $this->getAssoFromActivite($activite, $code, $limit);
    }
  }

  /**
   * R�cup�ration de la liste des actes incompatibles � l'acte
   *
   * @return void
   */
  function getActesIncomp() {
    $incomps    = array();
    $listIncomp = array();
    foreach ($this->_ref_code_ccam->_ref_incompatibilites as $dateEffet => $liste) {
      if ($dateEffet < $this->_date) {
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
   * R�cup�ration de la premi�re proc�dure li�e � l'acte
   *
   * @return void
   */
  function getProcedure() {
    $listProc = array();
    foreach ($this->_ref_code_ccam->_ref_procedures as $dateEffet => $liste) {
      if ($dateEffet < $this->_date) {
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
   * R�cup�ration du forfait d'un modificateur
   *
   * @param string $modificateur Lettre cl� du modificateur
   *
   * @return array forfait et coefficient
   */
  function getForfait($modificateur) {
    return CCodeCCAM::getForfait($modificateur);
  }

  /**
   * R�cup�ration du coefficient d'association
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
    return $this->chapitres[0]['db'] == '000018' && $this->chapitres[1]['db'] == '000002';
  }

  /**
   * Check wether an acte is a supplement or not
   *
   * @return bool
   */
  function isSupplement() {
    $this->getChaps();
    return $this->chapitres[0]['db'] == '000019' && $this->chapitres[1]['db'] == '000002';
  }

  /**
   * Check wether an acte is inclued in 'acte d'imagerie pour acte de radiologie interventionnelle
   * ou cardiologie interventionnelle'
   *
   * @return bool
   */
  function isRadioCardioInterv() {
    $this->getChaps();
    return $this->chapitres[0]['db'] == '000019' && $this->chapitres[1]['db'] == '000001'
      && $this->chapitres[2]['db'] == '000009' && $this->chapitres[3]['db'] == '000002';
  }

  /**
   * Recherche de codes CCAM
   *
   * @param string $code       Codes partiels � chercher
   * @param string $keys       Mot cl�s � chercher
   * @param int    $max_length Longueur maximum du code
   * @param string $where      Autres param�tres where
   *
   * @return array Tableau d'actes
   */
  function findCodes($code='', $keys='', $max_length = null, $where = null) {
    return CCodeCCAM::findCodes($code, $keys, $max_length, $where);
  }

  /**
   * R�cup�ration des actes radio
   *
   * @return array Tableau des actes
   */
  function getActeRadio() {
    return CCodeCCAM::getActeRadio($this->code);
  }
}
