<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class COperation extends CCodable implements IPatientRelated {
  // DB Table key
  var $operation_id  = null;

  // Clôture des actes
  var $cloture_activite_1    = null;
  var $cloture_activite_4    = null;

  // DB References
  var $sejour_id  = null;
  var $chir_id    = null;
  var $chir_2_id  = null;
  var $chir_3_id  = null;
  var $chir_4_id  = null;
  var $anesth_id  = null;
  var $plageop_id = null;
  var $salle_id   = null;
  var $poste_sspi_id = null;
  var $examen_operation_id = null;

  // DB Fields S@nté.com communication
  var $code_uf    = null;
  var $libelle_uf = null;

  // DB Fields
  var $date                 = null;
  var $libelle              = null;
  var $cote                 = null;
  var $temp_operation       = null;
  var $pause                = null;
  var $time_operation       = null;
  var $exam_extempo         = null;
  var $examen               = null;
  var $materiel             = null;
  var $commande_mat         = null;
  var $info                 = null;
  var $type_anesth          = null;
  var $rques                = null;
  var $rank                 = null;
  var $rank_voulu           = null;
  var $anapath              = null;
  var $flacons_anapath      = null;
  var $labo_anapath         = null;
  var $description_anapath  = null;
  var $labo                 = null;
  var $flacons_bacterio     = null;
  var $labo_bacterio         = null;
  var $description_bacterio = null;
  var $prothese             = null;
  var $ASA                  = null;
  var $position             = null;

  var $depassement        = null;
  var $conventionne       = null;
  var $forfait            = null;
  var $fournitures        = null;
  var $depassement_anesth = null;

  var $annulee = null;

  var $horaire_voulu      = null;
  var $_horaire_voulu     = null;
  var $duree_uscpo        = null;
  var $passage_uscpo      = null;
  var $duree_preop        = null;
  var $presence_preop     = null;
  var $presence_postop    = null;
  var $envoi_mail         = null;

  // Timings enregistrés
  var $debut_prepa_preop = null;
  var $fin_prepa_preop   = null;
  var $entree_bloc       = null;
  var $entree_salle      = null;
  var $pose_garrot       = null;
  var $debut_op          = null;
  var $fin_op            = null;
  var $retrait_garrot    = null;
  var $sortie_salle      = null;
  var $entree_reveil     = null;
  var $sortie_reveil_possible = null;
  var $sortie_reveil_reel = null;
  var $induction_debut   = null;
  var $induction_fin     = null;

  // Vérification du côté
  var $cote_admission      = null;
  var $cote_consult_anesth = null;
  var $cote_hospi          = null;
  var $cote_bloc           = null;

  // Visite de préanesthésie
  var $date_visite_anesth    = null;
  var $prat_visite_anesth_id = null;
  var $rques_visite_anesth   = null;
  var $autorisation_anesth   = null;

  // Form fields
  var $_hour_op         = null;
  var $_min_op          = null;
  var $_hour_urgence    = null;
  var $_min_urgence     = null;
  var $_lu_type_anesth  = null;
  var $_codes_ccam      = array();
  var $_duree_interv    = null;
  var $_duree_garrot    = null;
  var $_duree_induction = null;
  var $_presence_salle  = null;
  var $_duree_sspi      = null;
  var $_hour_voulu      = null;
  var $_min_voulu       = null;
  var $_deplacee        = null;
  var $_compteur_jour   = null;
  var $_pause_min       = null;
  var $_pause_hour      = null;
  var $_protocole_prescription_anesth_id = null;
  var $_protocole_prescription_chir_id   = null;
  var $_move                   = null;
  var $_reorder_rank_voulu     = null;
  var $_password_visite_anesth = null;
  var $_patient_id      = null;
  var $_dmi_alert       = null;
  var $_offset_uscpo    = array();
  var $_width_uscpo     = array();
  var $_width           = array();
  var $_debut_offset    = array();
  var $_fin_offset      = array();
  var $_place_after_interv_id = null;
  var $_heure_us        = null;
  var $_types_ressources_ids = null;
  var $_is_urgence      = null;

  // Distant fields
  var $_datetime          = null;
  var $_datetime_reel     = null;
  var $_datetime_reel_fin = null;
  var $_datetime_best     = null;
  var $_ref_affectation   = null;
  var $_ref_besoins       = null;

  // EAI Fields
  var $_eai_initiateur_group_id  = null; // group initiateur du message EAI

  // Links
  var $_link_editor = null;
  var $_link_viewer = null;

  /**
   * @var CMediusers
   */
  var $_ref_chir           = null;

  /**
   * @var CMediusers
   */
  var $_ref_chir_2         = null;

  /**
   * @var CMediusers
   */
  var $_ref_chir_3         = null;

  /**
   * @var CMediusers
   */
  var $_ref_chir_4         = null;

  /**
   * @var CPosteSSPI
   */
  var $_ref_poste          = null;

  /**
   * @var CPlageOp
   */
  var $_ref_plageop         = null;

  /**
   * @var CSalle
   */
  var $_ref_salle           = null;

  /**
   * @var CMediusers
   */
  var $_ref_anesth          = null;
  var $_ref_type_anesth     = null;
  var $_ref_consult_anesth  = null;
  var $_ref_anesth_visite   = null;

  /**
   * @var CActeCCAM[]
   */
  var $_ref_actes_ccam      = array();
  var $_ref_echange_hprim   = null;
  var $_ref_anesth_perops   = null;
  var $_ref_naissances      = null;
  var $_ref_poses_disp_vasc = null;

  // Filter Fields
  var $_date_min      = null;
  var $_date_max      = null;
  var $_plage         = null;
  var $_service       = null;
  var $_ranking       = null;
  var $_cotation      = null;
  var $_specialite    = null;
  var $_scodes_ccam   = null;
  var $_prat_id       = null;
  var $_bloc_id       = null;
  var $_ccam_libelle  = null;

  function COperation() {
    parent::__construct();
    $this->_locked = CAppUI::conf("dPplanningOp COperation locked");
  }

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'operations';
    $spec->key   = 'operation_id';
    $spec->measureable = true;
    $spec->events = array(
      "dhe" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
      "checklist" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
      "perop" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
      "liaison" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
      "sortie_reveil" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
    );
    return $spec;
  }

  function getProps() {
    $protocole = new CProtocole();
    $props = parent::getProps();
    $props["sejour_id"]            = "ref notNull class|CSejour";
    $props["chir_id"]              = "ref notNull class|CMediusers seekable";
    $props["chir_2_id"]            = "ref class|CMediusers seekable";
    $props["chir_3_id"]            = "ref class|CMediusers seekable";
    $props["chir_4_id"]            = "ref class|CMediusers seekable";
    $props["anesth_id"]            = "ref class|CMediusers";
    $props["plageop_id"]           = "ref class|CPlageOp seekable show|0";
    $props["pause"]                = "time show|0";
    $props["salle_id"]             = "ref class|CSalle";
    $props["poste_sspi_id"]        = "ref class|CPosteSSPI";
    $props["examen_operation_id"]  = "ref class|CExamenOperation";
    $props["date"]                 = "date";
    $props["code_uf"]              = "str length|3";
    $props["libelle_uf"]           = "str maxLength|35";
    $props["libelle"]              = "str seekable autocomplete dependsOn|chir_id";
    $props["cote"]                 = $protocole->_props["cote"] . " notNull default|inconnu";
    $props["temp_operation"]       = "time show|0";
    $props["debut_prepa_preop"]    = "time show|0";
    $props["fin_prepa_preop"]      = "time show|0";
    $props["entree_salle"]         = "time show|0";
    $props["sortie_salle"]         = "time show|0";
    $props["time_operation"]       = "time show|0";
    $props["examen"]               = "text helped";
    $props["exam_extempo"]         = "bool";
    $props["materiel"]             = "text helped seekable show|0";
    $props["commande_mat"]         = "bool show|0";
    $props["info"]                 = "bool";
    $props["type_anesth"]          = "ref class|CTypeAnesth";
    $props["rques"]                = "text helped";
    $props["rank"]                 = "num max|255 show|0";
    $props["rank_voulu"]           = "num max|255 show|0";
    $props["depassement"]          = "currency min|0 confidential show|0";
    $props["conventionne"]         = "bool default|1";
    $props["forfait"]              = "currency min|0 confidential show|0";
    $props["fournitures"]          = "currency min|0 confidential show|0";
    $props["depassement_anesth"]   = "currency min|0 confidential show|0";
    $props["annulee"]              = "bool show|0";
    $props["pose_garrot"]          = "time show|0";
    $props["debut_op"]             = "time show|0";
    $props["fin_op"]               = "time show|0";
    $props["retrait_garrot"]       = "time show|0";
    $props["entree_reveil"]        = "time show|0";
    $props["sortie_reveil_possible"] = "time show|0";
    $props["sortie_reveil_reel"]   = "time show|0";
    $props["induction_debut"]      = "time show|0";
    $props["induction_fin"]        = "time show|0";
    $props["entree_bloc"]          = "time show|0";
    $props["anapath"]              = "enum list|1|0|? default|? show|0";
    $props["flacons_anapath"]      = "num max|255 show|0";
    $props["labo_anapath"]         = "str autocomplete";
    $props["description_anapath"]  = "text helped";
    $props["labo"]                 = "enum list|1|0|? default|? show|0";
    $props["flacons_bacterio"]     = "num max|255 show|0";
    $props["labo_bacterio"]        = "str autocomplete";
    $props["description_bacterio"] = "text helped";
    $props["prothese"]             = "enum list|1|0|? default|? show|0";
    $props["position"]             = "enum list|DD|DV|DL|GP|AS|TO|GYN";
    $props["ASA"]                  = "enum list|1|2|3|4|5 default|1";
    $props["horaire_voulu"]        = "time show|0";
    $props["presence_preop"]       = "time show|0";
    $props["presence_postop"]      = "time show|0";
    $props["envoi_mail"]           = "dateTime show|0";

    // Clôture des actes
    $props["cloture_activite_1"]    = "bool default|0";
    $props["cloture_activite_4"]    = "bool default|0";

    $props["cote_admission"]      = $protocole->_props["cote"] . " show|0";
    $props["cote_consult_anesth"] = $protocole->_props["cote"] . " show|0";
    $props["cote_hospi"]          = $protocole->_props["cote"] . " show|0";
    $props["cote_bloc"]           = $protocole->_props["cote"] . " show|0";

    // Visite de préanesthésie
    $props["date_visite_anesth"]     = "date";
    $props["prat_visite_anesth_id"]  = "ref class|CMediusers";
    $props["rques_visite_anesth"]    = "text helped show|0";
    $props["autorisation_anesth"]    = "bool default|0";

    $props["facture"]                = "bool default|0";

    $props["duree_uscpo"]            = "num min|0 default|0";

    if (CAppUI::conf("dPplanningOp COperation show_duree_uscpo") == 2) {
      $props["passage_uscpo"]        = "bool notNull";
    }
    else {
      $props["passage_uscpo"]        = "bool";
    }

    $props["duree_preop"]             = "time";

    $props["_duree_interv"]           = "time";
    $props["_duree_garrot"]           = "time";
    $props["_duree_induction"]        = "time";
    $props["_presence_salle"]         = "time";
    $props["_duree_sspi"]             = "time";
    $props["_horaire_voulu"]          = "time";

    $props["_date_min"]               = "date";
    $props["_date_max"]               = "date moreEquals|_date_min";
    $props["_plage"]                  = "bool";

    $props["_ranking"]                = "enum list|ok|ko";
    $props["_cotation"]               = "enum list|ok|ko";

    $props["_prat_id"]                = "text"; // ?? misdefined
    $props["_patient_id"]             = "ref class|CPatient show|1";
    $props["_bloc_id"]                = "ref class|CBlocOperatoire";
    $props["_specialite"]             = "text";
    $props["_ccam_libelle"]           = "bool default|1";
    $props["_hour_op"]                = "";
    $props["_min_op"]                 = "";
    $props["_datetime"]               = "dateTime show";
    $props["_datetime_reel"]          = "dateTime";
    $props["_datetime_reel_fin"]      = "dateTime";
    $props["_datetime_best"]          = "dateTime";
    $props["_pause_min"]              = "numchar length|2";
    $props["_pause_hour"]             = "numchar length|2";
    $props["_move"]                   = "str";
    $props["_password_visite_anesth"] = "password notNull";
    $props["_heure_us"]               = "time";

    return $props;
  }

  function loadRelPatient(){
    return $this->loadRefPatient();
  }

  function getExecutantId($code_activite) {
    $this->loadRefChir();
    $this->loadRefPlageOp();
    return ($code_activite == 4 ? $this->_ref_anesth->user_id : $this->chir_id);
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["blood_salvages"]           = "CBloodSalvage operation_id";
    $backProps["dossiers_anesthesie"]      = "CConsultAnesth operation_id";
    $backProps["naissances"]               = "CNaissance operation_id";
    $backProps["prescription_elements"]    = "CPrescriptionLineElement operation_id";
    $backProps["prescription_medicaments"] = "CPrescriptionLineMedicament operation_id";
    $backProps["prescription_comments"]    = "CPrescriptionLineComment operation_id";
    $backProps["prescription_dmis"]        = "CPrescriptionLineDMI operation_id";
    $backProps["prescription_line_mix"]    = "CPrescriptionLineMix operation_id";
    $backProps["check_lists"]              = "CDailyCheckList object_id";
    $backProps["anesth_perops"]            = "CAnesthPerop operation_id";
    $backProps["echanges_hprim"]           = "CEchangeHprim object_id";
    $backProps["echanges_ihe"]             = "CExchangeIHE object_id";
    $backProps["product_orders"]           = "CProductOrder object_id";
    $backProps["op_brancardardage"]        = "CBrancardage operation_id";
    $backProps["besoins_ressources"]       = "CBesoinRessource operation_id";
    $backProps["poses_disp_vasc"]          = "CPoseDispositifVasculaire operation_id";
    return $backProps;
  }

  function getTemplateClasses(){
    $this->loadRefsFwd();

    $tab = array();

    // Stockage des objects liés à l'opération
    $tab['COperation'] = $this->_id;
    $tab['CSejour'] = $this->_ref_sejour->_id;
    $tab['CPatient'] = $this->_ref_sejour->_ref_patient->_id;

    $tab['CConsultation'] = 0;
    $tab['CConsultAnesth'] = 0;

    return $tab;
  }

  function check() {
    $msg = null;
    $this->completeField("chir_id", "plageop_id", "sejour_id");
    if (!$this->_id && !$this->chir_id) {
      $msg .= "Praticien non valide ";
    }

    // Bornes du séjour
    $sejour = $this->loadRefSejour();
    $this->loadRefPlageOp();

    if ($this->plageop_id !== null && !$sejour->entree_reelle) {
      $date = mbDate($this->_datetime);
      $entree = mbDate($sejour->entree_prevue);
      $sortie = mbDate($sejour->sortie_prevue);
      if (!CMbRange::in($date, $entree, $sortie)) {
         $msg .= "Intervention du $date en dehors du séjour du $entree au $sortie";
      }
    }

    // Vérification de la signature de l'anesthésiste pour la visite de pré-anesthésie
    if ($this->fieldModified("prat_visite_anesth_id") && $this->prat_visite_anesth_id !== null && $this->prat_visite_anesth_id != CAppUI::$user->_id) {
      $anesth = new CUser();
      $anesth->load($this->prat_visite_anesth_id);

      if (!CUser::checkPassword($anesth->user_username, $this->_password_visite_anesth)) {
        $msg .= "Mot de passe incorrect";
      }
    }

    return $msg . parent::check();
  }

  function delete() {
    $msg = parent::delete();
    $this->loadRefPlageOp();
    $this->_ref_plageop->reorderOp();
    return $msg;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_hour_op = intval(substr($this->temp_operation, 0, 2));
    $this->_min_op  = intval(substr($this->temp_operation, 3, 2));
    $this->_hour_urgence = intval(substr($this->time_operation, 0, 2));
    $this->_min_urgence  = intval(substr($this->time_operation, 3, 2));

    if ($this->horaire_voulu) {
      $this->_hour_voulu = intval(substr($this->horaire_voulu, 0, 2));
      $this->_min_voulu  = intval(substr($this->horaire_voulu, 3, 2));
      $this->_horaire_voulu = $this->horaire_voulu;
    }

    if ($this->pause) {
      $this->_pause_hour = intval(substr($this->pause, 0, 2));
      $this->_pause_min  = intval(substr($this->pause, 3, 2));
    }

    $this->_ref_type_anesth = $this->loadFwdRef("type_anesth", true);
    $this->_lu_type_anesth = $this->_ref_type_anesth->name;

    if ($this->debut_op && $this->fin_op && $this->fin_op > $this->debut_op) {
      $this->_duree_interv = mbSubTime($this->debut_op,$this->fin_op);
    }
    if ($this->pose_garrot && $this->retrait_garrot && $this->retrait_garrot > $this->pose_garrot) {
      $this->_duree_garrot = mbSubTime($this->pose_garrot,$this->retrait_garrot);
    }
    if ($this->induction_debut && $this->induction_fin && $this->induction_fin > $this->induction_debut) {
      $this->_duree_induction = mbSubTime($this->induction_debut,$this->induction_fin);
    }
    if ($this->entree_salle && $this->sortie_salle && $this->sortie_salle>$this->entree_salle) {
      $this->_presence_salle = mbSubTime($this->entree_salle,$this->sortie_salle);
    }
    if ($this->entree_reveil && $this->sortie_reveil_possible && $this->sortie_reveil_possible > $this->entree_reveil) {
      $this->_duree_sspi = mbSubTime($this->entree_reveil,$this->sortie_reveil_possible);
    }

    if ($this->plageop_id) {
      $this->_link_editor = "index.php?m=dPplanningOp&tab=vw_edit_planning&operation_id=".$this->_id;
    }
    else {
      $this->_link_editor = "index.php?m=dPplanningOp&tab=vw_edit_urgence&operation_id=".$this->_id;
    }

    $this->_acte_depassement        = $this->depassement;
    $this->_acte_depassement_anesth = $this->depassement_anesth;
  }

  function updatePlainFields() {
    if (is_array($this->_codes_ccam) && count($this->_codes_ccam)) {
      $this->codes_ccam = implode("|", $this->_codes_ccam);
    }

    if ($this->codes_ccam) {
      $this->codes_ccam = strtoupper($this->codes_ccam);
      $codes_ccam = explode("|", $this->codes_ccam);
      $XPosition = true;
      // @TODO: change it to use removeValue
      while ($XPosition !== false) {
        $XPosition = array_search("-", $codes_ccam);
        if ($XPosition !== false) {
          array_splice($codes_ccam, $XPosition, 1);
        }
      }
      $this->codes_ccam = implode("|", $codes_ccam);
    }
    if ($this->_hour_op !== null and $this->_min_op !== null) {
      $this->temp_operation = sprintf("%02d:%02d:00", $this->_hour_op, $this->_min_op);
    }
    if ($this->_hour_urgence !== null and $this->_min_urgence !== null) {
      $this->time_operation = sprintf("%02d:%02d:00", $this->_hour_urgence, $this->_min_urgence);
    }
    if ($this->_hour_voulu != null and $this->_min_voulu != null) {
      $this->horaire_voulu = sprintf("%02d:%02d:00", $this->_hour_voulu, $this->_min_voulu);
    }
    elseif ($this->_horaire_voulu) {
      $this->horaire_voulu = $this->_horaire_voulu;
    }
    if ($this->_pause_hour !== null and $this->_pause_min !== null) {
      $this->pause = sprintf("%02d:%02d:00", $this->_pause_hour, $this->_pause_min);
    }

    $this->completeField('rank', 'plageop_id');

    if ($this->_move) {
      $op = new COperation;
      $op->plageop_id = $this->plageop_id;

      switch ($this->_move) {
        case 'before':
          $op->rank = $this->rank-1;
          if ($op->loadMatchingObject()) {
            $op->rank = $this->rank;
            $op->store(false);
            $this->rank -= 1;
          }
        break;

        case 'after':
          $op->rank = $this->rank+1;
          if ($op->loadMatchingObject()) {
            $op->rank = $this->rank;
            $op->store(false);
            $this->rank += 1;
          }
        break;

        case 'out':
          $this->rank = 0;
          $this->time_operation = '00:00:00';
          $this->pause = '00:00:00';
        break;

        case 'last':
          if ($op->loadMatchingObject('rank DESC')) {
            $this->rank = $op->rank+1;
          }
        break;
        default;
      }

      $this->_reorder_rank_voulu = true;
      $this->_move = null;
    }
  }

  /**
   * Prepare the alert before storage
   *
   * @return string Alert comments if necessary, null if no alert
   */
  function prepareAlert() {
    // Création d'un alerte sur l'intervention
    $comments = null;
    if ($this->_old->rank || ($this->materiel && $this->commande_mat)) {
      $this->loadRefPlageOp();
      $this->_old->loadRefPlageOp();

      // Alerte sur l'annulation d'une intervention
      if ($this->fieldModified("annulee", "1")) {
        $comments .= "L'intervention a été annulée pour le ".mbTransformTime(null, $this->_datetime, CAppUI::conf("datetime")).".";
      }

      // Alerte sur le déplacement d'une intervention
      elseif (mbDate(null, $this->_datetime) != mbDate(null, $this->_old->_datetime)) {
        $comments .= "L'intervention a été déplacée du ".mbTransformTime(null, $this->_old->_datetime, CAppUI::conf("date"))." au ".mbTransformTime(null, $this->_datetime, CAppUI::conf("date")).".";
      }

      // Alerte sur la commande de matériel
      elseif ($this->fieldModified("materiel") && $this->commande_mat) {
        $comments .= "Le materiel a été modifié \n - Ancienne valeur : ".$this->_old->materiel." \n - Nouvelle valeur : ".$this->materiel;
      }

      // Aucune alerte
      else {
        return;
      }

      // Complément d'alerte
      if ($this->_old->rank) {
        $comments .= "\nL'intervention avait été validée.";
      }
      if ($this->materiel && $this->commande_mat) {
        $comments .= "\nLe materiel avait été commandé.";
      }
    }

    return $comments;
  }

  /**
   * Create an alert if comments is not empty
   *
   * @param string $comments Comments of the alert
   *
   * @return string Store-like message
   */
  function createAlert($comments) {
    if (!$comments) {
      return;
    }

    $alerte = new CAlert();
    $alerte->setObject($this);
    $alerte->comments = $comments;
    $alerte->tag = "mouvement_intervention";
    $alerte->handled = "0";
    $alerte->level = "medium";
    return $alerte->store();
  }

  function store($reorder = true) {
    $old_object = $this->loadOldObject();

    $this->completeField(
      "annulee",
      "rank",
      "codes_ccam",
      "plageop_id",
      "chir_id",
      "materiel",
      "commande_mat",
      "date"
    );

    // Problème après fusion si on a la date et la plage
    if ($this->date && $this->plageop_id) {
      $this->date = "";
    }

    // Si on choisit une plage, on copie la salle
    if ($this->fieldValued("plageop_id")) {
      $plage = $this->loadRefPlageOp();
      $this->salle_id = $plage->salle_id;
    }

    // Cas d'une plage que l'on quitte
    $old_plage = null;
    if ($this->fieldAltered("plageop_id") && $this->_old->rank) {
      $old_plage = $this->_old->loadRefPlageOp();
    }

    $comments = $this->prepareAlert();
    $place_after_interv_id = $this->_place_after_interv_id;
    $this->_place_after_interv_id = null;


    // Pré-remplissage de la durée préop si c'est une nouvelle intervention
    if (!$this->_id && !$this->duree_preop) {
      $patient = $this->loadRefSejour()->loadRefPatient();

      if ($patient->_annees >= 18) {
        $this->duree_preop = "00:" . CAppUI::conf("dPplanningOp COperation duree_preop_adulte") . ":00";
      }
      else {
        $this->duree_preop = "00:" . CAppUI::conf("dPplanningOp COperation duree_preop_enfant") . ":00";
      }
    }

    // On recopie la sortie réveil possible sur le réel si pas utilisée en config
    if (!CAppUI::conf("dPsalleOp COperation use_sortie_reveil_reel")) {
      $this->sortie_reveil_reel = $this->sortie_reveil_possible;
    }

    // Standard storage
    if ($msg = parent::store()) {
      return $msg;
    }

    // Création des besoins d'après le protocole sélectionné
    // Ne le faire que pour une nouvelle intervention
    // Pour une intervention existante, l'application du protocole
    // store les protocoles
    if (CAppUI::conf("dPbloc CPlageOp systeme_materiel") == "expert" &&
        $this->_types_ressources_ids && !$old_object->_id) {

      $types_ressources_ids = explode(",", $this->_types_ressources_ids);

      foreach ($types_ressources_ids as $_type_ressource_id) {
        $besoin = new CBesoinRessource;
        $besoin->type_ressource_id = $_type_ressource_id;
        $besoin->operation_id = $this->_id;
        if ($msg = $besoin->store()) {
          return $msg;
        }
      }
    }

    $this->createAlert($comments);

    $sejour = $this->loadRefSejour();
    $do_store_sejour = false; // Flag pour storer le séjour une seule fois

    // Mise à jour du type de PeC du séjour en Chirurgical si pas déja obstétrique
    $sejour->completeField("type_pec");
    if (!$this->_id && $sejour->type_pec != "O") {
      $sejour->type_pec = "C";
      $do_store_sejour = true;
    }

    // Cas d'une annulation
    if (!$this->annulee) {
      // Si pas une annulation on recupére le sejour
      // et on regarde s'il n'est pas annulé
      if ($sejour->annule) {
        $sejour->annule = 0;
        $do_store_sejour = true;
      }

      // Application des protocoles de prescription en fonction de l'operation->_id
      if ($this->_protocole_prescription_chir_id || $this->_protocole_prescription_anesth_id) {
        $sejour->_protocole_prescription_chir_id   = $this->_protocole_prescription_chir_id;
        $sejour->_protocole_prescription_anesth_id = $this->_protocole_prescription_anesth_id;
        $sejour->applyProtocolesPrescription($this->_id);

        // On les nullify pour eviter de les appliquer 2 fois
        $this->_protocole_prescription_anesth_id = null;
        $this->_protocole_prescription_chir_id   = null;
        $sejour->_protocole_prescription_chir_id   = null;
        $sejour->_protocole_prescription_anesth_id = null;
      }
    }
    elseif ($this->rank != 0 && !CAppUI::conf("dPplanningOp COperation save_rank_annulee_validee")) {
      $this->rank = 0;
      $this->time_operation = "00:00:00";
    }

    // Store du séjour (une seule fois)
    if ($do_store_sejour) {
      $sejour->store();
    }

    // Vérification qu'on a pas des actes CCAM codés obsolètes
    if ($this->codes_ccam) {
      $this->loadRefsActesCCAM();
      foreach ($this->_ref_actes_ccam as $keyActe => $acte) {
        if (stripos($this->codes_ccam, $acte->code_acte) === false) {
          $this->_ref_actes_ccam[$keyActe]->delete();
        }
      }
    }

    $reorder_rank_voulu = $this->_reorder_rank_voulu;
    $this->_reorder_rank_voulu = null;

    if ($this->plageop_id) {
      $plage = $this->loadRefPlageOp();
      // Cas de la création dans une plage de spécialité
      if ($plage->spec_id && $plage->unique_chir) {
        $plage->chir_id = $this->chir_id;
        $plage->spec_id = "";
        $plage->store();
      }

      // Placement de l'interv selon la preference (placement souhaité)
      if ($place_after_interv_id) {
        $plage->loadRefsOperations(false, "rank, rank_voulu, horaire_voulu", true);

        unset($plage->_ref_operations[$this->_id]);

        if ($place_after_interv_id == -1) {
          $reorder = true;
          $reorder_rank_voulu = true;
          $plage->_ref_operations = CMbArray::mergeKeys(array($this->_id => $this), $plage->_ref_operations); // To preserve keys (array_unshift does not)
        }
        elseif (isset($plage->_ref_operations[$place_after_interv_id])) {
          $reorder = true;
          $reorder_rank_voulu = true;
          CMbArray::insertAfterKey($plage->_ref_operations, $place_after_interv_id, $this->_id, $this);
        }

        if ($reorder_rank_voulu) {
          $plage->_reorder_up_to_interv_id = $this->_id;
        }
      }
    }

    // Standard storage bis
    if ($msg = parent::store()) {
      return $msg;
    }

    // Réordonnancement post-store
    if ($reorder) {
      // Réordonner la plage que l'on quitte
      if ($old_plage) {
        $old_plage->reorderOp();
      }

      $this->_ref_plageop->reorderOp($reorder_rank_voulu ? CPlageOp::RANK_REORDER : null);
    }
  }

  /**
   * Load list overlay for current group
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
    $ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
    // Filtre sur l'établissement
    $g = CGroups::loadCurrent();
    $where["sejour.group_id"] = "= '$g->_id'";

    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  function loadView() {
    parent::loadView();
    $this->loadRefPraticien()->loadRefFunction();
    $this->loadRefPatient();
    $this->_ref_sejour->_ref_patient->loadRefPhotoIdentite();
  }

  function loadComplete() {
    parent::loadComplete();
    $this->loadRefPatient();
    foreach ($this->_ref_actes_ccam as &$acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
  }

  /**
   * @return CMediusers
   */
  function loadRefChir($cache = true) {
    $this->_ref_chir = $this->loadFwdRef("chir_id", $cache);
    $this->_praticien_id = $this->_ref_chir->_id;
    return $this->_ref_chir;
  }

  /**
   * @param boolean $cache
   *
   * @return CMediusers
   */
  function loadRefChir2($cache = true) {
    return $this->_ref_chir_2 = $this->loadFwdRef("chir_2_id", $cache);
  }

  /**
   * @param boolean $cache
   *
   * @return CMediusers
   */
  function loadRefChir3($cache = true) {
    return $this->_ref_chir_3 = $this->loadFwdRef("chir_3_id", $cache);
  }

  /**
   * @param boolean $cache
   *
   * @return CMediusers
   */
  function loadRefChir4($cache = true) {
    return $this->_ref_chir_4 = $this->loadFwdRef("chir_4_id", $cache);
  }

  /**
   * @return CMediusers
   */
  function loadRefPraticien($cache = true) {
    $this->_ref_praticien = $this->loadRefChir($cache);
    $this->_ref_executant = $this->_ref_praticien;
    return $this->_ref_praticien;
  }

  function getActeExecution() {
    $this->loadRefPlageOp();
  }

  /**
   * @return CAffectation
   */
  function loadRefAffectation() {
    $this->loadRefPlageOp();
    $where = array();
    $where["sejour_id"] = "= $this->sejour_id";
    $where["entree"] = " <= '$this->_datetime'";
    $where["sortie"] = " >= '$this->_datetime'";
    $this->_ref_affectation = new CAffectation();
    $this->_ref_affectation->loadObject($where);
    $this->_ref_affectation->loadRefsFwd();
    $this->_ref_affectation->_ref_lit->loadRefsFwd();
    $this->_ref_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
    return $this->_ref_affectation;
  }

  function loadRefsNaissances() {
    return $this->_ref_naissances = $this->loadBackRefs("naissances");
  }


  function loadRefPoste() {
    return $this->_ref_poste = $this->loadFwdRef("poste_sspi_id");
  }

  /**
   * Met à jour les information sur la salle
   * Nécessiste d'avoir chargé la plage opératoire au préalable
   */
  function updateSalle() {
    if ($this->plageop_id && $this->salle_id) {
      $this->_deplacee = $this->_ref_plageop->salle_id != $this->salle_id;
    }

    // Evite de recharger la salle quand ce n'est pas nécessaire
    if ($this->plageop_id && !$this->_deplacee) {
      return $this->_ref_salle =& $this->_ref_plageop->_ref_salle;
    }
    else {
      $salle = new CSalle;
      return $this->_ref_salle = $salle->getCached($this->salle_id);
    }
  }

  function loadRefAnesth($cache = true) {
    if($this->anesth_id) {
      return $this->_ref_anesth = $this->loadFwdRef("anesth_id", $cache);
    }
    if($this->plageop_id) {
      return $this->_ref_anesth = $this->_ref_plageop->loadFwdRef("anesth_id", $cache);
    }
    return $this->_ref_anesth = new CMediusers();
  }

  /**
   * @return CPlageOp
   */
  function loadRefPlageOp($cache = true) {

    $this->_ref_anesth_visite = $this->loadFwdRef("prat_visite_anesth_id", $cache);

    if (!$this->_ref_plageop) {
      $this->_ref_plageop = $this->loadFwdRef("plageop_id", $cache);
    }
    $plageOp = $this->_ref_plageop;

    // Avec plage d'opération
    if ($plageOp->_id) {
      $plageOp->loadRefsFwd($cache);

      if ($this->anesth_id) {
        $this->loadRefAnesth();
      } else {
        $this->_ref_anesth = $plageOp->_ref_anesth;
      }

      $date = $plageOp->date;
    }
    // Hors plage
    else {
      $this->loadRefAnesth();
      $date = $this->date;
    }

    $this->updateSalle();

    //Calcul du nombre de jour entre la date actuelle et le jour de l'operation
    $this->_compteur_jour = mbDaysRelative($date, mbDate());

    // Horaire global
    if ($this->time_operation && $this->time_operation != "00:00:00") {
      $this->_datetime = "$date $this->time_operation";
    }
    elseif ($this->horaire_voulu && $this->horaire_voulu != "00:00:00") {
      $this->_datetime = "$date $this->horaire_voulu";
    }
    elseif ($plageOp->_id) {
      $this->_datetime = "$date ".$plageOp->debut;
    }
    else {
      $this->_datetime = "$date 00:00:00";
    }
    $this->_datetime_best     = $this->_datetime;
    $this->_datetime_reel     = "$date $this->debut_op";
    if ($this->debut_op) {
      $this->_datetime_best = $this->_datetime_reel;
    }
    $this->_datetime_reel_fin = "$date $this->fin_op";

    // Heure standard d'exécution des actes
    if ($this->fin_op) {
      $this->_acte_execution = $this->_datetime_reel_fin;
    }
    elseif ($this->debut_op) {
      $this->_acte_execution = mbAddDateTime($this->temp_operation, $this->_datetime_reel);
    }
    elseif ($this->time_operation != "00:00:00") {
      $this->_acte_execution = mbAddDateTime($this->temp_operation, $this->_datetime);
    }
    else {
      $this->_acte_execution = $this->_datetime;
    }

    $this->_view = "Intervention ";

    if ($this->date) {
      $this->_view .= "(hors plage) ";
    }

    $this->_view .= "du " . mbTransformTime(null, $this->_datetime, CAppUI::conf("date"));
    return $this->_ref_plageop;
  }

  function preparePossibleActes() {
    $this->loadRefPlageOp();
  }

  /**
   * @return CConsultAnesth
   */
  function loadRefsConsultAnesth() {
    if ($this->_ref_consult_anesth) {
      return $this->_ref_consult_anesth;
    }

    $order = "consultation_anesth_id ASC";
    return $this->_ref_consult_anesth = @$this->loadUniqueBackRef("dossiers_anesthesie", $order);
  }

  /**
   * @return CSejour
   */
  function loadRefSejour($cache = true) {
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", $cache);
  }

  /**
   * Chargement des gestes perop
   */
  function loadRefsAnesthPerops(){
    return $this->_ref_anesth_perops = $this->loadBackRefs("anesth_perops", "datetime");
  }

  /**
   * Chargement des poses de dispositif vasculaire
   */
  function loadRefsPosesDispVasc($count_check_lists = false){
    $this->_ref_poses_disp_vasc = $this->loadBackRefs("poses_disp_vasc", "date");

    if ($count_check_lists) {
      foreach ($this->_ref_poses_disp_vasc as $_pose) {
        $_pose->countSignedCheckLists();
      }
    }

    return $this->_ref_poses_disp_vasc;
  }

  /**
   * @return CPatient
   */
  function loadRefPatient($cache = true) {
    $sejour = $this->loadRefSejour($cache);
    $patient = $sejour->loadRefPatient($cache);
    $this->_ref_patient = $patient;
    $this->_patient_id = $patient->_id;
    $this->loadFwdRef("_patient_id", $cache);
    return $patient;
  }

  function loadRefsFwd($cache = true) {
    $consult_anesth = $this->loadRefsConsultAnesth();
    $consult_anesth->countDocItems();

    $consultation = $consult_anesth->loadRefConsultation();
    $consultation->countDocItems();
    $consultation->canRead();
    $consultation->canEdit();

    $this->loadRefPlageOp($cache);
    $this->loadExtCodesCCAM();

    $this->loadRefChir($cache)->loadRefFunction();
    $this->loadRefPatient($cache);
    $this->_view = "Intervention de {$this->_ref_sejour->_ref_patient->_view} par le Dr {$this->_ref_chir->_view}";
  }

  function loadRefsBesoins() {
    return $this->_ref_besoins = $this->loadBackRefs("besoins_ressources");
  }

  function loadRefsBack() {
    $this->loadRefsFiles();
    $this->loadRefsActes();
    $this->loadRefsDocs();
  }

  /**
   * @return bool
   */
  function isCoded() {
    $this->loadRefPlageOp();
    $this->_coded = (CAppUI::conf("dPsalleOp COperation modif_actes") == "never") ||
                    (CAppUI::conf("dPsalleOp COperation modif_actes") == "oneday" && $this->date > mbDate()) ||
                    (CAppUI::conf("dPsalleOp COperation modif_actes") == "button" && $this->_ref_plageop->actes_locked) ||
                    (CAppUI::conf("dPsalleOp COperation modif_actes") == "facturation" && $this->facture);
    return $this->_coded;
  }

  function getPerm($permType) {
    switch ($permType) {
      case PERM_EDIT :
        if (!$this->_ref_chir) {
          $this->loadRefChir();
        }

        if (!$this->_ref_anesth) {
          $this->loadRefPlageOp();
        }

        if ($this->plageop_id) {
          return (($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType)) && $this->_ref_module->getPerm($permType));
        }
        else {
          return (($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType)) && $this->_ref_module->getPerm(PERM_READ));
        }
        break;
      default :
        return parent::getPerm($permType);
    }

    //if (!$this->_ref_chir) {
    //  $this->loadRefChir();
    //}
    //if (!$this->_ref_anesth) {
    //  $this->loadRefPlageOp();
    //}
    //return ($this->_ref_chir->getPerm($permType) || $this->_ref_anesth->getPerm($permType));
  }

  function fillTemplate(&$template) {
    $this->loadRefsFwd();
    $this->_ref_sejour->loadRefsFwd();

    // Chargement du fillTemplate du praticien
    $this->_ref_chir->fillTemplate($template);

    // Chargement du fillTemplate du sejour
    $this->_ref_sejour->fillTemplate($template);

    // Chargement du fillTemplate de l'opération
    $this->fillLimitedTemplate($template);
  }

  function fillLimitedTemplate(&$template) {
    $this->loadRefsFwd(1);
    $this->loadRefPraticien();
    $this->loadRefsFiles();
    $this->loadAffectationsPersonnel();

    $plageop = $this->_ref_plageop;
    $plageop->loadAffectationsPersonnel();

    foreach ($this->_ext_codes_ccam as $_code) {
      $_code->getRemarques();
      $_code->getActivites();
    }

    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addProperty("Opération - Chirurgien"           , $this->_ref_praticien->_id ? ("Dr ".$this->_ref_praticien->_view) : '');
    $template->addProperty("Opération - Anesthésiste - nom"   , @$this->_ref_anesth->_user_last_name);
    $template->addProperty("Opération - Anesthésiste - prénom", @$this->_ref_anesth->_user_first_name);
    $template->addProperty("Opération - Anesthésie"           , $this->_lu_type_anesth);
    $template->addProperty("Opération - libellé"              , $this->libelle);
    $template->addProperty("Opération - CCAM1 - code"         , @$this->_ext_codes_ccam[0]->code);
    $template->addProperty("Opération - CCAM1 - description"  , @$this->_ext_codes_ccam[0]->libelleLong);
    $template->addProperty("Opération - CCAM1 - montant activité 1", @$this->_ext_codes_ccam[0]->activites[1]->phases[0]->tarif);
    $template->addProperty("Opération - CCAM1 - montant activité 4", @$this->_ext_codes_ccam[0]->activites[4]->phases[0]->tarif);
    $template->addProperty("Opération - CCAM2 - code"         , @$this->_ext_codes_ccam[1]->code);
    $template->addProperty("Opération - CCAM2 - description"  , @$this->_ext_codes_ccam[1]->libelleLong);
    $template->addProperty("Opération - CCAM2 - montant activité 1", @$this->_ext_codes_ccam[1]->activites[1]->phases[0]->tarif);
    $template->addProperty("Opération - CCAM2 - montant activité 4", @$this->_ext_codes_ccam[1]->activites[4]->phases[0]->tarif);
    $template->addProperty("Opération - CCAM3 - code"         , @$this->_ext_codes_ccam[2]->code);
    $template->addProperty("Opération - CCAM3 - description"  , @$this->_ext_codes_ccam[2]->libelleLong);
    $template->addProperty("Opération - CCAM3 - montant activité 1", @$this->_ext_codes_ccam[2]->activites[1]->phases[0]->tarif);
    $template->addProperty("Opération - CCAM3 - montant activité 4", @$this->_ext_codes_ccam[2]->activites[4]->phases[0]->tarif);
    $template->addProperty("Opération - CCAM - codes"         , implode(" - ", $this->_codes_ccam));
    $template->addProperty("Opération - CCAM - descriptions"  , implode(" - ", CMbArray::pluck($this->_ext_codes_ccam, "libelleLong")));
    $template->addProperty("Opération - salle"                , @$this->_ref_salle->nom);
    $template->addProperty("Opération - côté"                 , $this->cote);

    $template->addDateProperty("Opération - date"             , $this->_datetime_best != " 00:00:00" ? $this->_datetime_best : "");
    $template->addLongDateProperty("Opération - date longue"  , $this->_datetime_best != " 00:00:00" ? $this->_datetime_best : "");
    $template->addTimeProperty("Opération - heure"            , $this->time_operation);
    $template->addTimeProperty("Opération - durée"            , $this->temp_operation);
    $template->addTimeProperty("Opération - durée réelle"     , $this->_duree_interv);
    $template->addTimeProperty("Opération - entrée bloc"      , $this->entree_salle);
    $template->addTimeProperty("Opération - pose garrot"      , $this->pose_garrot);
    $template->addTimeProperty("Opération - début op"         , $this->debut_op);
    $template->addTimeProperty("Opération - fin op"           , $this->fin_op);
    $template->addTimeProperty("Opération - retrait garrot"   , $this->retrait_garrot);
    $template->addTimeProperty("Opération - sortie bloc"      , $this->sortie_salle);

    $template->addProperty("Opération - depassement"          , $this->depassement);
    $template->addProperty("Opération - exams pre-op"         , $this->examen);
    $template->addProperty("Opération - matériel"             , $this->materiel);
    $template->addProperty("Opération - convalescence"        , $this->_ref_sejour->convalescence);
    $template->addProperty("Opération - remarques"            , $this->rques);

    $template->addBarcode("Opération - Code Barre ID"         , $this->_id);

    $list = CMbArray::pluck($this->_ref_files, "file_name");
    $template->addListProperty("Opération - Liste des fichiers", $list);

    foreach ($this->_ref_affectations_personnel as $emplacement => $affectations) {
      $locale = CAppUI::tr("CPersonnel.emplacement.$emplacement");
      $property = implode(" - ", CMbArray::pluck($affectations, "_ref_personnel", "_ref_user", "_view"));
      $template->addProperty("Opération - personnel réel - $locale", $property);
    }

    foreach ($plageop->_ref_affectations_personnel as $emplacement => $affectations) {
      $locale = CAppUI::tr("CPersonnel.emplacement.$emplacement");
      $property = implode(" - ", CMbArray::pluck($affectations, "_ref_personnel", "_ref_user", "_view"));
      $template->addProperty("Opération - personnel prévu - $locale", $property);
    }

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  function getDMIAlert(){
    if (!CModule::getActive("dmi")) {
      return;
    }

    $this->_dmi_prescription_id = null;
    $this->_dmi_praticien_id    = null;

    $lines = $this->loadBackRefs("prescription_dmis");

    if (empty($lines)) {
      return $this->_dmi_alert = "none";
    }

    $auto_validate = CAppUI::conf("dmi CDMI auto_validate");
    if ($auto_validate) {
      return $this->_dmi_alert = "ok";
    }

    foreach ($lines as $_line) {
      if (!isset($this->_dmi_prescription_id)) {
        $this->_dmi_prescription_id = $_line->prescription_id;
        $this->_dmi_praticien_id    = $_line->loadRefPrescription()->praticien_id;
      }

      if ($_line->type != "purchase" && !$_line->isValidated()) {
        return $this->_dmi_alert = "warning";
      }
    }

    return $this->_dmi_alert = "ok";
  }

  function updateHeureUS() {
    $this->_heure_us = $this->duree_preop ? mbSubTime($this->duree_preop, $this->time_operation) : $this->time_operation;
  }

  function getAffectation() {
    $sejour = $this->_ref_sejour;

    if (!$this->_ref_sejour) {
      $sejour = $this->loadRefSejour();
    }

    if (!$this->_datetime_best) {
      $this->loadRefPlageOp();
    }

    $affectation = new CAffectation();
    $order = "entree";
    $where = array();

    $where["sejour_id"] = "= '$this->sejour_id'";

    $moment = $this->_datetime_best;

    // Si l'intervention est en dehors du séjour,
    // on recadre dans le bon intervalle
    if ($moment < $sejour->entree) {
      $moment = $sejour->entree;
    }

    if ($moment > $sejour->sortie) {
      $moment = $sejour->sortie;
    }

    if (mbTime(null, $moment) == "00:00:00") {
      $where["entree"] = $this->_spec->ds->prepare("<= %", mbDate(null, $moment)." 23:59:59");
      $where["sortie"] = $this->_spec->ds->prepare(">= %", mbDate(null, $moment)." 00:00:01");
    }
    else {
      $where["entree"] = $this->_spec->ds->prepare("<= %", $moment);
      $where["sortie"] = $this->_spec->ds->prepare(">= %", $moment);
    }

    $affectation->loadObject($where, $order);

    return $affectation;
  }

  function docsEditable() {
    if (parent::docsEditable()) {
      return true;
    }

    $fix_edit_doc = CAppUI::conf("dPplanningOp CSejour fix_doc_edit");
    $this->loadRefSejour();
    return !$fix_edit_doc ? true : $this->_ref_sejour->sortie_reelle === null;
  }
}
