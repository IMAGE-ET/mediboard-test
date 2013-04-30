<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/*
 * CCodable
 */
class CCodable extends CMbObject {
  public $codes_ccam;
  public $facture; // Séjour facturé ou non

  // Form fields
  public $_acte_execution;
  public $_acte_depassement;
  public $_acte_depassement_anesth;
  public $_ref_anesth;
  public $_anesth;
  public $_associationCodesActes;
  public $_count_actes;
  public $_actes_non_cotes;
  public $_datetime;

  // Abstract fields
  public $_praticien_id;
  public $_coded = 0;    // Initialisation à 0 => codable qui peut etre codé !

  // Actes CCAM
  public $_text_codes_ccam;
  public $_codes_ccam;
  public $_tokens_ccam;

  /** @var CActeCCAM[]  */
  public $_ref_actes_ccam;

  /** @var CCodeCCAM[] */
  public $_ext_codes_ccam;

  public $_temp_ccam;

  // Actes NGAP
  public $_store_ngap;

  /** @var CActeNGAP[] */
  public $_ref_actes_ngap;

  public $_codes_ngap;
  public $_tokens_ngap;

  // Actes Tarmed
  public $_codes_tarmed;

  /** @var CActeTarmed[] */
  public $_ref_actes_tarmed;

  public $_tokens_tarmed;

  // Actes Caisse
  public $_codes_caisse;

  /** @var CActeCaisse[] */
  public $_ref_actes_caisse;

  public $_tokens_caisse;

  // Back references
  public $_ref_actes;
  public $_ref_prescriptions;

  /** @var  CFraisDivers[] */
  public $_ref_frais_divers;

  // Distant references
  public $_ref_sejour;
  public $_ref_patient;
  public $_ref_praticien;
  public $_ref_executant;

  // Behaviour fields
  public $_delete_actes;

  /**
   * Détruit les actes CCAM et NGAP
   *
   * @return string Store-like message
   */
  function deleteActes() {
    $this->_delete_actes = false;

    // Suppression des anciens actes CCAM
    $this->loadRefsActesCCAM();
    foreach ($this->_ref_actes_ccam as $acte) {
      if ($msg = $acte->delete()) {
        return $msg;
      }
    }
    $this->codes_ccam = "";

    // Suppression des anciens actes NGAP
    $this->loadRefsActesNGAP();
    foreach ($this->_ref_actes_ngap as $acte) {
      if ($msg = $acte->delete()) {
        return $msg;
      }
    }
    $this->_tokens_ngap = "";

    if (CModule::getActive("tarmed")) {
      // Suppression des anciens actes Tarmed
      $this->loadRefsActesTarmed();
      foreach ($this->_ref_actes_tarmed as $acte) {
        if ($msg = $acte->delete()) {
          return $msg;
        }
      }
      $this->_tokens_tarmed = "";

      $this->loadRefsActesCaisse();
      foreach ($this->_ref_actes_caisse as $acte) {
        if ($msg = $acte->delete()) {
          return $msg;
        }
      }
      $this->_tokens_caisse = "";
    }
    return null;
  }

  /**
   * Store redefinition
   *
   * @return string Store-like message
   */
  function store() {
    if ($this instanceof CSejour || $this instanceof COperation) {
      global $can;
      $this->loadOldObject();
      $this->completeField("cloture_activite_1", "cloture_activite_4");

      if (!$can->admin && CAppUI::conf("dPsalleOp CActeCCAM signature") &&
          ($this->cloture_activite_1 || $this->cloture_activite_4) &&
          $this->fieldModified("codes_ccam") &&
          strcmp($this->codes_ccam, $this->_old->codes_ccam)) {
        $new_code = substr($this->codes_ccam, strlen($this->_old->codes_ccam)+1);

        $code_ccam = new CCodeCCAM($new_code);
        $code_ccam->getRemarques();
        $activites = $code_ccam->getActivites();

        if (isset($activites[1]) && $this->cloture_activite_1) {
          CAppUI::setMsg("Impossible de rajouter un code : l'activité 1 est clôturée", UI_MSG_ERROR);
          echo CAppUI::getMsg();
          CApp::rip();
        }
        if (isset($activites[4]) && $this->cloture_activite_4) {
          CAppUI::setMsg("Impossible de rajouter un code : l'activité 4 est clôturée", UI_MSG_ERROR);
          echo CAppUI::getMsg();
          CApp::rip();
        }
      }
    }

    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }

    if ($this->_delete_actes && $this->_id) {
      if ($msg = $this->deleteActes()) {
        return $msg;
      }
    }
    return null;
  }

  /**
   * @return CSejour
   */
  function loadRefSejour() {
  }

  /**
   * @return CPatient
   */
  function loadRefPatient() {
  }

  /**
   * @return CMediusers
   */
  function loadRefPraticien() {

  }

  function loadView() {
    parent::loadView();
    $this->loadRefsActesCCAM();
    $this->loadExtCodesCCAM(true);
  }

  function getActeExecution() {
    $this->_acte_execution = CMbDT::dateTime();
  }

  function isCoded() {
    return $this->_coded;
  }

  function updateFormFields() {
    parent::updateFormFields();

    $this->codes_ccam = strtoupper($this->codes_ccam);
    $this->_text_codes_ccam = str_replace("|", ", ", $this->codes_ccam);
    $this->_codes_ccam = $this->codes_ccam ?
      explode("|", $this->codes_ccam) :
      array();
  }

  function getProps() {
    $props = parent::getProps();
    $props["codes_ccam"]   = "str show|0";
    $props["facture"]      = "bool default|0";

    $props["_tokens_ccam"]    = "";
    $props["_tokens_ngap"]    = "";
    $props["_tokens_tarmed"]  = "";
    $props["_tokens_caisse"]  = "";
    $props["_codes_ccam"]     = "";
    $props["_codes_ngap"]     = "";
    $props["_codes_tarmed"]   = "";
    $props["_codes_caisse"]   = "";
    $props["_count_actes"] = "num min|0";
    return $props;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["actes_ngap"]    = "CActeNGAP object_id";
    $backProps["actes_ccam"]    = "CActeCCAM object_id";
    $backProps["actes_tarmed"]  = "CActeTarmed object_id";
    $backProps["actes_caisse"]  = "CActeCaisse object_id";
    $backProps["frais_divers"]  = "CFraisDivers object_id";
    return $backProps;
  }

  /*
  function loadRefPrescription() {
    $this->_ref_prescription = $this->loadUniqueBackRef("prescriptions");
  }
  */
  function getAssociationCodesActes() {
    $this->updateFormFields();
    $this->loadRefsActesCCAM();
    if ($this->_ref_actes_ccam) {
      foreach ($this->_ref_actes_ccam as $_acte) {
        $_acte->loadRefExecutant();
      }
    }
    $this->_associationCodesActes = array();
    $listCodes = $this->_ext_codes_ccam;
    $listActes = $this->_ref_actes_ccam;
    foreach ($listCodes as $key_code => $_code) {
      $ccam     = $_code->code;
      $phase    = $_code->_phase;
      $activite = $_code->_activite;
      $this->_associationCodesActes[$key_code]["code"]    = $_code->code;
      $this->_associationCodesActes[$key_code]["nbActes"] = 0;
      $this->_associationCodesActes[$key_code]["ids"]     = "";
      foreach ($listActes as $key_acte => $_acte) {
        $test = ($_acte->code_acte == $ccam);
        $test = $test && ($phase === null || $_acte->code_phase == $phase);
        $test = $test && ($activite === null || $_acte->code_activite == $activite);
        $test = $test && !isset($this->_associationCodesActes[$key_code]["actes"][$_acte->code_phase][$_acte->code_activite]);
        if ($test) {
          $this->_associationCodesActes[$key_code]["actes"][$_acte->code_phase][$_acte->code_activite] = $_acte;
          $this->_associationCodesActes[$key_code]["nbActes"]++;
          $this->_associationCodesActes[$key_code]["ids"] .= "$_acte->_id|";
          unset($listActes[$key_acte]);
        }
      }
    }
  }

  function updateDBCodesCCAMField() {
    if (null !== $this->_codes_ccam) {
      $this->codes_ccam = implode("|", $this->_codes_ccam);
    }
  }


  function doUpdateMontants(){

  }

  function updatePlainFields() {
    // Should update codes CCAM. Very sensible, test a lot before uncommenting
    // $this->updateDBCodesCCAMField();
  }

  function preparePossibleActes() {
  }

  function getExecutantId($code_activite) {
    return null;
  }
  
  function getExtensionDocumentaire() {
    return null;
  }

  function countActes($user_id = null) {
    $where = array();
    if ($user_id) {
      $where["executant_id"] = "= '$user_id'";
    }
    $this->_count_actes = 0;
    $this->_count_actes += $this->countBackRefs("actes_ngap", $where);
    $this->_count_actes += $this->countBackRefs("actes_ccam", $where);
    $this->_count_actes += $this->countBackRefs("actes_tarmed", $where);
    $this->_count_actes += $this->countBackRefs("actes_caisse", $where);
  }

  function correctActes() {
    $this->loadRefsActes();

    foreach ($this->_ref_actes_ccam as $_acte) {
      $_acte->guessAssociation();
      if ($_acte->_guess_association != "X") {
        $_acte->code_association = $_acte->_guess_association;
        $_acte->_calcul_montant_base = true;
        $_acte->store();
      }
    }
  }

  function loadRefsActes(){
    $this->_ref_actes = array();

    $this->loadRefsActesCCAM();
    $this->loadRefsActesNGAP();
    $this->loadRefsActesTarmed();
    $this->loadRefsActesCaisse();

    foreach ($this->_ref_actes_ccam as $acte_ccam) {
      $this->_ref_actes[] = $acte_ccam;
    }

    foreach ($this->_ref_actes_ngap as $acte_ngap) {
      $this->_ref_actes[] = $acte_ngap;
    }

    if ($this->_ref_actes_tarmed) {
      foreach ($this->_ref_actes_tarmed as $acte_tarmed) {
        $this->_ref_actes[] = $acte_tarmed;
      }
    }

    if ($this->_ref_actes_caisse) {
      foreach ($this->_ref_actes_caisse as $acte_caisse) {
        $this->_ref_actes[] = $acte_caisse;
      }
    }

    $this->_count_actes = count($this->_ref_actes);
  }

  /**
   * Charge les actes CCAM codés
   * 
   * @return CActeCCAM[]
   */
  function loadRefsActesCCAM() {
    if ($this->_ref_actes_ccam) {
      return $this->_ref_actes_ccam;
    }

    $order = array();
    $order[] = "code_association";
    $order[] = "code_acte";
    $order[] = "code_activite";
    $order[] = "code_phase";
    $order[] = "acte_id";

    if (null === $this->_ref_actes_ccam = $this->loadBackRefs("actes_ccam", $order)) {
      return $this->_ref_actes_ccam;
    }

    $this->_temp_ccam = array();
    foreach ($this->_ref_actes_ccam as $_acte_ccam) {
      $this->_temp_ccam[] = $_acte_ccam->makeFullCode();
    }

    $this->_tokens_ccam = implode("|", $this->_temp_ccam);
    return $this->_ref_actes_ccam;
  }

  /**
   * Charge les actes NGAP codés
   * 
   * @return CActeNGAP[]
   */
  function loadRefsActesNGAP() {
    /** ajout d'un paramètre d'ordre à passer, ici "lettre_cle" qui vaut 0 ou 1
     * la valeur 1 étant pour les actes principaux et O pour les majorations
     * on souhaite que les actes principaux soient proritaires( donc '1' avant '0')
     * */
    if (null === $this->_ref_actes_ngap = $this->loadBackRefs("actes_ngap", "lettre_cle DESC")) {
      return;
    }

    $this->_codes_ngap = array();
    foreach ($this->_ref_actes_ngap as $_acte_ngap) {
      $this->_codes_ngap[] = $_acte_ngap->makeFullCode();
      $_acte_ngap->loadRefExecutant();
      $_acte_ngap->getLibelle();
    }
    $this->_tokens_ngap = implode("|", $this->_codes_ngap);
  }

  /**
   * Charge les actes Tarmed codés
   *
   * @return array
   */
  function loadRefsActesTarmed(){
    $this->_ref_actes_tarmed = array();
    $totaux = array("base" => 0, "dh" => 0);

    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
      $where = array();
      $ljoin = array();
      $order = null;
      $acte_tarmed = new CActeTarmed();
      //Dans le cas d'une consultation
      if ($this->_class == "CConsultation") {
        //Classement des actes par ordre chonologique et par code
        $ljoin["consultation"] = "acte_tarmed.object_id = consultation.consultation_id";
        $ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";

        $where["acte_tarmed.object_class"] = " = '$this->_class'";
        $where["acte_tarmed.object_id"] = " = '$this->_id'";

        //Dans le cas ou la date est nulle on prend celle de la plage de consultation correspondante
        $order = "IFNULL(acte_tarmed.date, plageconsult.date) ,code ASC";

        $this->_ref_actes_tarmed = $acte_tarmed->loadList($where, $order, null, null, $ljoin );
      }
      //Dans les cas d'un séjour ou d'une intervention
      else {
        $where["object_class"] = " = '$this->_class'";
        $where["object_id"]    = " = '$this->_id'";
        $order = "code ASC";
        $this->_ref_actes_tarmed = $acte_tarmed->loadList($where, $order);
      }

      if (null === $this->_ref_actes_tarmed) {
        return null;
      }

      $this->_codes_tarmed = array();
      foreach ($this->_ref_actes_tarmed as $_acte_tarmed) {
        $this->_codes_tarmed[] = $_acte_tarmed->makeFullCode();
        $_acte_tarmed->loadRefExecutant();
        $_acte_tarmed->loadRefTarmed();
        $_acte_tarmed->countActesAssocies();
        $totaux["base"] += $_acte_tarmed->montant_base;
        $totaux["dh"]   += $_acte_tarmed->montant_depassement;
      }
      $this->_tokens_tarmed = implode("|", $this->_codes_tarmed);
    }
    return $totaux;
  }

  /**
   * Charge les actes Caisse codés
   *
   * @return array 
   */
  function loadRefsActesCaisse(){
    $this->_ref_actes_caisse = array();
    $totaux = array("base" => 0, "dh" => 0);

    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
      //Classement des actes par ordre chonologique et par code
      $where = array();
      $where["acte_caisse.object_class"] = " = '$this->_class'";
      $where["acte_caisse.object_id"] = " = '$this->_id'";

      $order = "caisse_maladie_id, code ASC";
      $acte_caisse = new CActeCaisse();
      $this->_ref_actes_caisse = $acte_caisse->loadList($where, $order);

      if (null === $this->_ref_actes_caisse) {
        return null;
      }

      $this->_codes_caisse = array();
      foreach ($this->_ref_actes_caisse as $_acte_caisse) {
        $this->_codes_caisse[] = $_acte_caisse->makeFullCode();
        $_acte_caisse->loadRefExecutant();
        $_acte_caisse->loadRefPrestationCaisse();
        $_acte_caisse->loadRefCaisseMaladie();
        $totaux["base"] += $_acte_caisse->montant_base;
        $totaux["dh"]   += $_acte_caisse->montant_depassement;
      }
      $this->_tokens_caisse = implode("|", $this->_codes_caisse);
    }
    return $totaux;
  }

  /**
   * Charge les codes CCAM en tant qu'objets externes
   * 
   * @param string $full niveau de chargement
   * 
   * @return void
   */
  function loadExtCodesCCAM($full = false) {
    $this->_ext_codes_ccam = array();
    if ($this->_codes_ccam !== null) {
      foreach ($this->_codes_ccam as $code) {
        $this->_ext_codes_ccam[] = CCodeCCAM::get($code, $full ? CCodeCCAM::FULL : CCodeCCAM::LITE);
      }
    }
  }

  function loadRefsFraisDivers(){
    $this->_ref_frais_divers = $this->loadBackRefs("frais_divers");
    foreach ($this->_ref_frais_divers as $_frais) {
      $_frais->loadRefType();
    }
    return $this->_ref_frais_divers;
  }

  function getMaxCodagesActes() {
    if (!$this->_id || $this->codes_ccam === null) {
      return null;
    }

    $oldObject = new $this->_class;
    $oldObject->load($this->_id);
    $oldObject->codes_ccam = $this->codes_ccam;
    $oldObject->updateFormFields();

    $oldObject->loadRefsActesCCAM();

    // Creation du tableau minimal de codes ccam
    $codes_ccam_minimal = array();
    foreach ($oldObject->_ref_actes_ccam as $acte) {
      $codes_ccam_minimal[$acte->code_acte] = true;
    }

    // Transformation du tableau de codes ccam
    $codes_ccam = array();
    foreach ($oldObject->_codes_ccam as $code) {
      if (strlen($code) > 7) {
        // si le code est de la forme code-activite-phase
        $detailCode = explode("-", $code);
        $code = $detailCode[0];
      }
      $codes_ccam[$code] = true;
    }

    // Test entre les deux tableaux
    foreach (array_keys($codes_ccam_minimal) as $_code) {
      if (!array_key_exists($_code, $codes_ccam)) {
        return "Impossible de supprimer le code";
      }
    }
    return null;
  }

  function checkCodeCcam() {
    $codes_ccam = explode("|", $this->codes_ccam);
    CMbArray::removeValue("", $codes_ccam);
    foreach ($codes_ccam as $_code_ccam) {
      if (!preg_match("/^[A-Z]{4}[0-9]{3}(-[0-9](-[0-9])?)?$/i", $_code_ccam)) {
        return "Le code CCAM '$_code_ccam' n'est pas valide";
      }
    }
    return null;
  }

  function check() {
    if ($msg = $this->checkCodeCcam()) {
      return $msg;
    }

    //@todo: why not use $this->_old ?
    $oldObject = new $this->_class;
    if ($this->_id) {
      $oldObject->load($this->_id);
    }

    if (!$this->_forwardRefMerging && !$this->_merging && CAppUI::conf("dPccam CCodable use_getMaxCodagesActes")) {
      if ($this->codes_ccam != $oldObject->codes_ccam) {
        if ($msg = $this->getMaxCodagesActes()) {
          return $msg;
        }
      }
    }

    return parent::check();
  }

  function testCloture() {
    $actes_ccam = $this->loadRefsActesCCAM();

    $count_activite_1 = 0;
    $count_activite_4 = 0;

    foreach ($actes_ccam as $_acte_ccam) {
      if ($_acte_ccam->code_activite == 1) {
        $count_activite_1 ++;
      }
      if ($_acte_ccam->code_activite == 4) {
        $count_activite_4 ++;
      }
    }

    return ($count_activite_1 == 0 || $this->cloture_activite_1) &&
           ($count_activite_4 == 0 || $this->cloture_activite_4);
  }

  function checkModificateur($code, $heure) {
    $keys = array("A", "E",  "P", "S", "U", "7");

    if (!in_array($code, $keys)) return null;

    $patient   = $this->_ref_patient;
    $discipline = $this->_ref_praticien->_ref_discipline;
    // Il faut une date complête pour la comparaison
    $date_ref = CMbDT::date();
    $date = "$date_ref $heure";

    switch ($code) {
      case "A":
        return ($patient->_annees < 4 || $patient->_annees > 80);
        break;
      case "E":
        return $patient->_annees < 5;
        break;
      case "P":
        return in_array($discipline->text, array("MEDECINE GENERALE", "PEDIATRIE")) &&
          (($date > "$date_ref 20:00:00" && $date <= "$date_ref 23:59:59") ||
           ($date > "$date_ref 06:00:00" && $date < "$date_ref 08:00:00"));
        break;
      case "S":
        return in_array($discipline->text, array("MEDECINE GENERALE", "PEDIATRIE")) &&
          ($date >= "$date_ref 00:00:01" && $date < "$date_ref 06:00:00");
        break;
      case "U":
        $date_tomorrow = CMbDT::date("+1 day", $date_ref)." 08:00:00";
        return !in_array($discipline->text, array("MEDECINE GENERALE", "PEDIATRIE")) &&
          ($date > "$date_ref 20:00:00" && $date < $date_tomorrow);
        break;
      case "7":
        return CAppUI::conf("dPccam CCodable precode_modificateur_7");
    }
    return null;
  }

  /**
   * Charge les actes CCAM codables en fonction des code CCAM fournis
   * 
   * @return void
   */
  function loadPossibleActes () {
    $this->preparePossibleActes();
    $depassement_affecte        = false;
    $depassement_anesth_affecte = false;
    // existing acts may only be affected once to possible acts
    $used_actes = array();

    $this->loadRefPatient()->evalAge();
    $this->loadRefPraticien()->loadRefDiscipline();

    $this->loadExtCodesCCAM(true);
    foreach ($this->_ext_codes_ccam as $code_ccam) {
      foreach ($code_ccam->activites as &$activite) {
        foreach ($activite->phases as &$phase) {

          $possible_acte = new CActeCCAM();
          $possible_acte->montant_depassement = "";
          $possible_acte->code_acte = $code_ccam->code;
          $possible_acte->code_activite = $activite->numero;

          $possible_acte->_anesth = ($activite->numero == 4);

          $possible_acte->code_phase = $phase->phase;
          $possible_acte->execution = $this->_acte_execution;

          // Affectation du dépassement au premier acte de chirugie
          if (!$depassement_affecte and $possible_acte->code_activite == 1) {
            $depassement_affecte = true;
            $possible_acte->montant_depassement = $this->_acte_depassement;
          }

          // Affectation du dépassement au premier acte d'anesthésie
          if (!$depassement_anesth_affecte and $possible_acte->code_activite == 4) {
            $depassement_anesth_affecte = true;
            $possible_acte->montant_depassement = $this->_acte_depassement_anesth;
          }

          $possible_acte->executant_id = CAppUI::pref("user_executant") ?
            CMediusers::get()->_id :
            $this->getExecutantId($possible_acte->code_activite);
          
          if($possible_acte->code_activite == 4) {
            $possible_acte->extension_documentaire = $this->getExtensionDocumentaire();
          }

          $possible_acte->updateFormFields();
          $possible_acte->loadRefs();
          $possible_acte->getAnesthAssocie();

          // Affect a loaded acte if exists
          foreach ($this->_ref_actes_ccam as $_acte) {
            if ($_acte->code_acte     == $possible_acte->code_acte
                && $_acte->code_activite == $possible_acte->code_activite
                && $_acte->code_phase    == $possible_acte->code_phase) {
              if (!isset($used_actes[$_acte->acte_id])) {
                $possible_acte = $_acte;
                $used_actes[$_acte->acte_id] = true;
                break;
              }
            }
          }

          $possible_acte->guessAssociation();
          $possible_acte->getTarif();

          // Keep references !
          $phase->_connected_acte = $possible_acte;
          if (!$possible_acte->_id) {
            foreach ($phase->_modificateurs as &$modificateur) {
              $modificateur->_checked = $this->checkModificateur($modificateur->code, CMbDT::time($phase->_connected_acte->execution));
            }
          }
          else {
            foreach ($phase->_modificateurs as &$modificateur) {
              if (strpos($phase->_connected_acte->modificateurs, $modificateur->code) !== false) {
                $modificateur->_checked = $modificateur->code;
              }
              else {
                $modificateur->_checked = "";
              }
            }
          }
        }
      }
    }
  }
  
  /**
   * Ajout des actes non ccam d'un tarif dans une intervention ou consultation 
   * 
   * @param string $token      les tokens
   * @param string $acte_class la classe des actes pris en compte
   * @param string $chir       l'executant de l'acte
   * 
   * @return string $msg
   */
  function precodeActe($token, $acte_class, $chir) {
    $listCodes = explode("|", $this->$token);
    foreach ($listCodes as $code) {
      if ($code) {
        $acte = new $acte_class;
        $acte->_preserve_montant = true;
        $acte->setFullCode($code);

        $acte->object_id = $this->_id;
        $acte->object_class = $this->_class;
        $acte->executant_id = $chir;
        if (!$acte->countMatchingList()) {
          if ($msg = $acte->store()) {
            return $msg;
          }
        }
      }
    }
    return null;
  }
  
  /**
   * Ajout des actes ccam d'un tarif dans une intervention ou consultation 
   * 
   * @param string $chir l'executant de l'acte
   * 
   * @return string $msg
   */
  function precodeCCAM($chir) {
    // Explode des codes_ccam du tarif
    $listCodesCCAM = explode("|", $this->codes_ccam);
    foreach ($listCodesCCAM as $code) {
      $acte = new CActeCCAM();
      $acte->_adapt_object = true;

      $acte->_preserve_montant = true;
      $acte->setFullCode($code);

      // si le code ccam est composé de 3 elements, on le precode
      if ($acte->code_activite != "" && $acte->code_phase != "") {
        // Permet de sauvegarder le montant de base de l'acte CCAM
        $acte->_calcul_montant_base = 1;

        // Mise a jour de codes_ccam suivant les _tokens_ccam du tarif
        $acte->object_id = $this->_id;
        $acte->object_class = $this->_class;
        $acte->executant_id = $chir;
        $acte->execution = $this->_datetime;
        $acte->facturable = 1;
        if ($msg = $acte->store()) {
          return $msg;
        }
      }
    }
    return null;
  }
}
