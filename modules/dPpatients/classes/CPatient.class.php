<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * The CPatient Class
 */
class CPatient extends CMbObject {
  static $dossier_cabinet_prefix = array (
    "dPcabinet" => "?m=dPcabinet&tab=vw_dossier&patSel=",
    "dPpatients" => "?m=dPpatients&tab=vw_full_patients&patient_id="
  );

  // http://www.msa47.fr/front/id/msa47/S1153385038825/S1153385272497/S1153385352251
  static $libelle_exo_guess = array(
    "code_exo" => array(
      //0 => null,
      4 => array(
        "affection",
        "ald",
        "hors liste"
      ),
      /*3 => array(
        "stérilité",
        "prématuré",
        "HIV"
      ),*/
      5 => array(
        "rente AT",
        "pension d'invalidité",
        "pension militaire",
        "enceinte",
        "maternité",
      ),
      9 => array(
        "FSV",
        "FNS",
        "vieillesse"
      ),
    ),
    "_art115" => array(
      true => array(
        "pension militaire"
      )
    ),
    "_type_exoneration" => array(
      "aldHorsListe" =>  array("hors liste"),
      "aldListe" =>      array("ald liste"),
      "aldMultiple" =>   array("ald multiple"),
      "alsaceMoselle" => array("alsace", "moselle"),
      "article115" =>    array("pension militaire"),
      "fns" =>           array("fns", "fsv", "vieillesse"),
      "autreCas" =>      array(),
      "autreCasAlsaceMoselle" => array(),
    ),
  );

  static $rangToQualBenef = array(
    "01" => 0,
    "31" => 1,
    "02" => 2,
    "11" => 6,
  );

  // DB Table key
  var $patient_id = null;

  // DB Fields
  var $nom              = null;
  var $nom_jeune_fille  = null;
  var $prenom           = null;
  var $prenom_2         = null;
  var $prenom_3         = null;
  var $prenom_4         = null;
  var $nom_soundex2     = null;
  var $nomjf_soundex2   = null;
  var $prenom_soundex2  = null;
  var $naissance        = null;
  var $deces            = null;
  var $sexe             = null;
  var $civilite         = null;
  var $adresse          = null;
  var $ville            = null;
  var $cp               = null;
  var $tel              = null;
  var $tel2             = null;
  var $tel_autre        = null;
  var $email            = null;
  var $vip              = null;
  var $tutelle          = null;

  var $medecin_traitant_declare = null;
  var $medecin_traitant = null;
  var $incapable_majeur = null;
  var $ATNC             = null;
  var $matricule        = null;
  var $INSC             = null;
  var $avs              = null;

  var $code_regime      = null;
  var $caisse_gest      = null;
  var $centre_gest      = null;
  var $code_gestion     = null;
  var $centre_carte     = null;
  var $regime_sante     = null;
  var $rques            = null;
  var $cmu              = null;
  var $ald              = null;
  var $code_exo         = null;
  var $libelle_exo    = null;
  var $notes_amo        = null;
  var $notes_amc        = null;
  var $deb_amo          = null;
  var $fin_amo          = null;
  var $code_sit         = null;
  var $regime_am        = null;
  var $mutuelle_types_contrat = null;

  var $rang_beneficiaire= null;
  var $qual_beneficiaire= null; // LogicMax, VitaleVision
  var $rang_naissance   = null;
  var $fin_validite_vitale = null;

  var $pays                 = null;
  var $pays_insee           = null;
  var $lieu_naissance       = null;
  var $cp_naissance         = null;
  var $pays_naissance_insee = null;
  var $profession           = null;
  var $csp                  = null; // Catégorie socioprofessionnelle
  var $patient_link_id      = null; // Patient link

  // Assuré
  var $assure_nom                   = null;
  var $assure_nom_jeune_fille       = null;
  var $assure_prenom                = null;
  var $assure_prenom_2              = null;
  var $assure_prenom_3              = null;
  var $assure_prenom_4              = null;
  var $assure_naissance             = null;
  var $assure_sexe                  = null;
  var $assure_civilite              = null;
  var $assure_adresse               = null;
  var $assure_ville                 = null;
  var $assure_cp                    = null;
  var $assure_tel                   = null;
  var $assure_tel2                  = null;
  var $assure_pays                  = null;
  var $assure_pays_insee            = null;
  var $assure_cp_naissance          = null;
  var $assure_lieu_naissance        = null;
  var $assure_pays_naissance_insee  = null;
  var $assure_profession            = null;
  var $assure_rques                 = null;
  var $assure_matricule             = null;
  var $assure_avs                   = null;

  // Other fields
  var $INSC_date                    = null;
  var $date_lecture_vitale          = null;
  var $_pays_naissance_insee        = null;
  var $_assure_pays_naissance_insee = null;

  // Behaviour fields
  var $_anonyme                     = null;
  var $_generate_IPP                = true;

  // Form fields
  var $_vip           = null;
  var $_annees        = null;
  var $_age           = null;
  var $_age_assure    = null;
  var $_civilite      = null;
  var $_civilite_long = null;
  var $_assure_civilite = null;
  var $_assure_civilite_long = null;
  var $_longview      = null;
  var $_art115        = null;
  var $_type_exoneration = null;
  var $_exoneration = null;
  var $_can_see_photo = null;
  var $_csp_view      = null;
  var $_nb_enfants    = null;
  var $_overweight    = null;
  var $_age_min       = null;
  var $_age_max       = null;

  // Vitale behaviour
  var $_bind_vitale   = null;
  var $_update_vitale = null;
  var $_id_vitale     = null;

  //ean (for switzerland)
  var $_assuranceCC_ean   = null;
  var $_assureCC_id       = null;
  var $_assuranceCC_id    = null;

  // Navigation Fields
  var $_dossier_cabinet_url = null;

  // EAI Fields
  var $_eai_initiateur_group_id  = null; // group initiateur du message EAI

  // HPRIM Fields
  var $_prenoms          = null; // multiple
  var $_nom_naissance    = null; // +/- = nom_jeune_fille
  var $_adresse_ligne2   = null;
  var $_adresse_ligne3   = null;
  var $_pays             = null;
  var $_IPP              = null;
  var $_fusion           = null; // fusion
  var $_patient_elimine  = null; // fusion

  var $_nb_docs                     = null;

  /**
   * @var CSejour[]
   */
  var $_ref_sejours                 = null;

  /**
   * @var CConsultation[]
   */
  var $_ref_consultations           = null;
  var $_ref_prescriptions           = null;
  var $_ref_grossesses              = null;
  var $_ref_last_grossesse          = null;
  var $_ref_first_constantes        = null;
  var $_ref_patient_links           = null;

  /**
   * @var CAffectation
   */
  var $_ref_curr_affectation        = null;

  /**
   * @var CAffectation
   */
  var $_ref_next_affectation        = null;

  /**
   * @var CMedecin
   */
  var $_ref_medecin_traitant        = null;

  /**
   * @var CCorrespondant[]
   */
  var $_ref_medecins_correspondants = null;

  /**
   * @var CCorrespondantPatient[]
   */
  var $_ref_correspondants_patient  = null;
  var $_ref_cp_by_relation          = null;

  /**
   * @var CDossierMedical
   */
  var $_ref_dossier_medical         = null;
  var $_refs_devenirs_dentaires     = null;
  var $_ref_IPP                     = null;
  var $_ref_vitale_idsante400       = null;
  var $_ref_constantes_medicales    = null;

  // Distant fields
  var $_ref_praticiens = null; // Praticiens ayant participé à la pec du patient

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'patients';
    $spec->key   = 'patient_id';
    $spec->measureable = true;
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["constantes"]            = "CConstantesMedicales patient_id";
    $backProps["consultations"]         = "CConsultation patient_id";
    $backProps["correspondants"]        = "CCorrespondant patient_id";
    $backProps["correspondants_patient"] = "CCorrespondantPatient patient_id";
    $backProps["hprim21_patients"]      = "CHprim21Patient patient_id";
    $backProps["prescriptions_labo"]    = "CPrescriptionLabo patient_id";
    $backProps["product_deliveries"]    = "CProductDelivery patient_id";
    $backProps["sejours"]               = "CSejour patient_id";
    $backProps["dossier_medical"]       = "CDossierMedical object_id";
    $backProps["echanges_hprim"]        = "CEchangeHprim object_id";
    $backProps["echanges_hprim21"]      = "CEchangeHprim21 object_id";
    $backProps["echanges_ihe"]          = "CExchangeIHE object_id";
    $backProps["devenirs_dentaires"]    = "CDevenirDentaire patient_id";
    $backProps["correspondants_courrier"] = "CCorrespondantCourrier object_id";
    $backProps["grossesses"]            = "CGrossesse parturiente_id";
    $backProps["facture_patient"]       = "CFactureConsult patient_id";
    $backProps["patient_observation_result_sets"] = "CObservationResultSet patient_id"; // interfere avec CMbObject-back-observation_result_sets
    $backProps["patient_links"]         = "CPatient patient_link_id";
    return $backProps;
  }

  function getProps() {
    $props = parent::getProps();

    $props["nom"]               = "str notNull confidential seekable|begin";
    $props["prenom"]            = "str notNull seekable|begin";
    $props["prenom_2"]          = "str";
    $props["prenom_3"]          = "str";
    $props["prenom_4"]          = "str";
    $props["nom_jeune_fille"]   = "str confidential seekable|begin";
    $props["nom_soundex2"]      = "str";
    $props["prenom_soundex2"]   = "str";
    $props["nomjf_soundex2"]    = "str";
    $props["medecin_traitant_declare"] = "bool";
    $props["medecin_traitant"]  = "ref class|CMedecin";
    $props["matricule"]         = CAppUI::conf("dPpatients CPatient check_code_insee") ? "code insee confidential mask|9S99S99S9xS999S999S99" : "str maxLength|8";
    $props["INSC"]              = "str length|22";
    $props["code_regime"]       = "numchar length|2";
    $props["caisse_gest"]       = "numchar length|3";
    $props["centre_gest"]       = "numchar length|4";
    $props["code_gestion"]      = "str length|2";
    $props["centre_carte"]      = "numchar length|4";
    $props["regime_sante"]      = "str";
    $props["sexe"]              = "enum list|m|f";
    $props["civilite"]          = "enum list|m|mme|mlle|enf|dr|pr|me|vve";
    $props["adresse"]           = "text confidential";
    $props["ville"]             = "str confidential seekable|begin";
    $props["cp"]                = "str minLength|4 maxLength|5 confidential";
    $props["tel"]               = "phone confidential";
    $props["tel2"]              = "phone confidential";
    $props["tel_autre"]         = "str maxLength|20";
    $props["email"]             = "str confidential";
    $props["vip"]               = "bool default|0";
    $props["tutelle"]           = "enum list|aucune|tutelle|curatelle default|aucune";
    $props["incapable_majeur"]  = "bool";
    $props["ATNC"]              = "bool";
    $props["avs"]               = "str maxLength|16";// mask|999.99.999.999";

    $conf = CAppUI::conf("dPpatients CPatient identitovigilence");
    $props["naissance"] = $conf === "date" || $conf === "doublons" ? "birthDate notNull" : "birthDate";

    $props["deces"]             = "date progressive";
    $props["rques"]             = "text";
    $props["cmu"]               = "bool";
    $props["ald"]               = "bool";
    $props["code_exo"]          = "enum list|0|4|5|9 default|0";
    $props["libelle_exo"]        = "text";
    $props["deb_amo"]           = "date";
    $props["fin_amo"]           = "date";
    $props["notes_amo"]         = "text";
    $props["notes_amc"]         = "text";
    $props["rang_beneficiaire"] = "enum list|01|02|09|11|12|13|14|15|16|31";
    $props["qual_beneficiaire"] = "enum list|0|1|2|3|4|5|6|7|8|9";
    $props["rang_naissance"]    = "enum list|1|2|3|4|5|6 default|1";
    $props["fin_validite_vitale"] = "date";
    $props["code_sit"]          = "numchar length|4";
    $props["regime_am"]         = "bool default|0";
    $props["mutuelle_types_contrat"] = "text";

    $props["pays"]                 = "str";
    $props["pays_insee"]           = "str";
    $props["lieu_naissance"]       = "str";
    $props["cp_naissance"]         = "str minLength|4 maxLength|5 confidential";
    $props["pays_naissance_insee"] = "str";
    $props["profession"]           = "str autocomplete";
    $props["csp" ]                 = "numchar length|2";
    $props["patient_link_id"]      = "ref class|CPatient";

    $props["assure_nom"]                  = "str confidential";
    $props["assure_prenom"]               = "str";
    $props["assure_prenom_2"]             = "str";
    $props["assure_prenom_3"]             = "str";
    $props["assure_prenom_4"]             = "str";
    $props["assure_nom_jeune_fille"]      = "str confidential";
    $props["assure_sexe"]                 = "enum list|m|f";
    $props["assure_civilite"]             = "enum list|m|mme|mlle|enf|dr|pr|me|vve";
    $props["assure_naissance"]            = "birthDate confidential mask|99/99/9999 format|$3-$2-$1";
    $props["assure_adresse"]              = "text confidential";
    $props["assure_ville"]                = "str confidential";
    $props["assure_cp"]                   = "str minLength|4 maxLength|5 confidential";
    $props["assure_tel"]                  = "phone confidential";
    $props["assure_tel2"]                 = "phone confidential";
    $props["assure_pays"]                 = "str";
    $props["assure_pays_insee"]           = "str";
    $props["assure_lieu_naissance"]       = "str";
    $props["assure_cp_naissance"]         = "str minLength|4 maxLength|5 confidential";
    $props["assure_pays_naissance_insee"] = "str";
    $props["assure_profession"]           = "str autocomplete";
    $props["assure_rques"]                = "text";
    $props["assure_matricule"]            = "code insee confidential mask|9S99S99S99S999S999S99";
    $props["assure_avs"]                  = "str maxLength|15";
    $props["INSC_date"]                   = "dateTime";
    $props["date_lecture_vitale"]         = "dateTime";
    $props["_id_vitale"]                  = "num";
    $props["_pays_naissance_insee"]       = "str";
    $props["_assure_pays_naissance_insee"]= "str";
    $props["_art115"]                     = "bool";

    $types_exo = array(
      "aldHorsListe",
      "aldListe",
      "aldMultiple",
      "alsaceMoselle",
      "article115",
      "autreCas",
      "autreCasAlsaceMoselle",
      "fns",
    );

    $props["_type_exoneration"]           = "enum list|".implode("|", $types_exo);
    $props["_annees"]                     = "num show|1";
    $props["_age"]                        = "str";
    $props["_vip"]                        = "bool";
    $props["_age_assure"]                 = "num";

    $props["_age_min"]                    = "num min|0";
    $props["_age_max"]                    = "num min|0";

    $props["_assuranceCC_id"]             = "str length|5";
    $props["_assureCC_id"]                = "str maxLength|20";
    $props["_assuranceCC_ean"]            = "str";

    $props["_IPP"]                        = "str show|1";

    return $props;
  }

  function checkMerge($patients = array()/*<CPatient>*/) {
    if ($msg = parent::checkMerge($patients)) {
      return $msg;
    }

    $sejour = new CSejour;
    $where["patient_id"] = CSQLDataSource::prepareIn(CMbArray::pluck($patients, "_id"));
    $sejours = $sejour->loadList($where);

    foreach ($sejours as $_sejour1) {
      foreach ($sejours as $_sejour2) {
        if ($_sejour1->collides($_sejour2)) {
          $_sejour1->loadRefPatient(1);
          $_sejour2->loadRefPatient(1);
          return CAppUI::tr("CPatient-merge-warning-venue-conflict", $_sejour1->_view, $_sejour2->_view);
        }
      }
    }
  }

  /**
   * @param self[] $objects
   * @param bool   $fast
   *
   * @return CMbObject|CModelObject
   */
  function merge($objects = array/*<CPatient>*/(), $fast = false) {
    // Load the matching CDossierMedical objects
    if ($this->_id) {
      $merged_objects = array_merge($objects, array($this));
    }
    else {
      $merged_objects = $objects;
    }

    $where = array(
      'object_class' => "='$this->_class'",
      'object_id'    => CSQLDataSource::prepareIn(CMbArray::pluck($merged_objects, 'patient_id'))
    );
    $dossier_medical = new CDossierMedical();
    $list = $dossier_medical->loadList($where);

    foreach ($objects as $object) {
      $object->loadIPP();
    }

    if ($msg = parent::merge($objects, $fast)) {
      return $msg;
    }

    // Merge them
    if (count($list) > 1) {
      if ($msg = $dossier_medical->mergePlainFields($list)) {
        return $msg;
      }

      $dossier_medical->object_class = $this->_class;
      $dossier_medical->object_id = $this->_id;
      return $dossier_medical->merge($list);
    }
  }

  function check(){
    // Standard check
    if ($msg = parent::check()) {
      return $msg;
    }

    // Creation d'un patient
    if (!$this->_merging && CAppUI::conf('dPpatients CPatient identitovigilence') == "doublons") {
      if ($this->loadMatchingPatient(true, false) > 0) {
        return "Doublons détectés";
      }
    }
  }

  function store() {
    //$this->INSC      = "1075102722581011056235";
    //$this->INSC_date = " 2012-03-12 16:59:21";

    $this->completeField("patient_link_id");
    if ($this->_id && $this->_id == $this->patient_link_id) {
      $this->patient_link_id = "";
    }

    if ($this->fieldModified("naissance") || $this->fieldModified("sexe")) {
      // _guid is not valued yet !!
      SHM::remKeys("bcb-alertes-*-CPatient-".$this->_id);
    }

    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }

    if ($this->checkAnonymous()) {
      $this->nom = $this->_id;
      $this->_anonyme = true;
      $this->store();
    }

    // Vitale
    if (CModule::getActive("fse")) {
      $cv = CFseFactory::createCV();
      if ($cv) {
        if ($msg = $cv->bindVitale($this)) {
          return $msg;
        }
      }
    }

    // Génération de l'IPP ?
    if ($this->_generate_IPP) {
      if ($msg = $this->generateIPP()) {
        return $msg;
      }
    }
  }

  function generateIPP() {
    $group = CGroups::loadCurrent();
    $group->loadConfigValues();
    if ($group->_configs["sip_idex_generator"]) {
      $this->loadIPP($group->_id);
      if ($this->_IPP) {
        return;
      }
      if (!$IPP = CIncrementer::generateIdex($this, self::getTagIPP($group->_id), $group->_id)) {
        return CAppUI::tr("CIncrementer_undefined");
      }
    }
  }

  function guessExoneration(){
    $this->completeField("libelle_exo");

    if (!$this->libelle_exo) {
      return;
    }

    foreach (self::$libelle_exo_guess as $field => $values) {
      if ($this->$field !== null) {
        continue;
      }

      foreach ($values as $value => $rules) {
        foreach ($rules as $rule) {
          if (preg_match("/$rule/i", $this->libelle_exo)) {
            $this->$field = $value;
            break;
          }
        }
      }
    }
  }

  function updateFormFields() {
    parent::updateFormFields();

    // Noms
    $this->nom = CMbString::upper($this->nom);
    $this->nom_jeune_fille = CMbString::upper($this->nom_jeune_fille);
    $this->prenom = CMbString::capitalize(CMbString::lower($this->prenom));

    $this->_nom_naissance = $this->nom_jeune_fille ? $this->nom_jeune_fille : $this->nom;
    $this->_prenoms = array($this->prenom, $this->prenom_2, $this->prenom_3, $this->prenom_4);

    if ($this->libelle_exo) {
      $this->_art115 = preg_match("/pension militaire/i", $this->libelle_exo);
    }

    $relative = CMbDate::relative($this->naissance);

    if ($relative["count"] < 0) {
      $relative["count"] = 0;
    }

    $this->evalAge();

    $str = $relative["unit"] . ($relative["count"] > 1 ? "s" : "") .($relative["unit"] == "year" ? "_old" : "");
    $this->_age = $relative["count"] . " " . CAppUI::tr($str);

    $this->checkVIP();

    $this->_civilite = CAppUI::tr("CPatient.civilite.$this->civilite");
    if ($this->civilite === "enf") {
      $this->_civilite_long = CAppUI::tr("CPatient.civilite.".($this->sexe === "m" ? "le_jeune" : "la_jeune"));
    }
    else {
      $this->_civilite_long = CAppUI::tr("CPatient.civilite.$this->civilite-long");
    }

    $this->_assure_civilite = CAppUI::tr("CPatient.civilite.$this->assure_civilite");
    if ($this->assure_civilite === "enf") {
      $this->_assure_civilite_long = CAppUI::tr("CPatient.civilite.".($this->assure_sexe === "m" ? "le_jeune" : "la_jeune"));
    }
    else {
      $this->_assure_civilite_long = CAppUI::tr("CPatient.civilite.$this->assure_civilite-long");
    }

    $nom_naissance   = $this->nom_jeune_fille && $this->nom_jeune_fille != $this->nom ? " ($this->nom_jeune_fille)" : "";
    $this->_view     = "$this->_civilite $this->nom$nom_naissance $this->prenom";
    $this->_longview = "$this->_civilite_long $this->nom$nom_naissance $this->prenom";
    $this->_view .= $this->vip ? " [Conf.]" : "";
    $this->_view .= $this->deces ? " [Décès.]" : "";
    $this->_longview .= $this->vip ? " [Conf.]" : "";
    $this->_longview .= $this->deces ? " [Décès.]" : "";

    // Navigation fields
    //$this->_dossier_cabinet_url = self::$dossier_cabinet_prefix[CAppUI::pref("DossierCabinet")] . $this->_id;
    $this->_dossier_cabinet_url = self::$dossier_cabinet_prefix["dPpatients"] . $this->_id;

    if ($this->pays_insee && !$this->pays) {
      $this->pays = $this->updatePatNomPays($this->pays_insee);
    }

    if ($this->csp) {
      $this->_csp_view = $this->getCSPName();
    }
  }

  /**
   * Calcul l'âge du patient en années
   *
   * @param string $date Date de référence pour le calcul, maintenant si null
   *
   * @return int l'age du patient en années
   */
  function evalAge($date = null) {
    $achieved = CMbDate::achievedDurations($this->naissance, $date);
    return $this->_annees = $achieved["year"];
  }

  /**
   * Calcul l'aspect confidentiel du patient
   *
   * @return bool on a accès ou pas
   */
  function checkVIP() {
    if ($this->_vip !== null) {
      return;
    }

    $this->_vip = false;
    $user = CMediusers::get();

    if ($this->vip&& !CModule::getCanDo("dPpatient")->admin()) {

      // Test si le praticien est présent dans son dossier

      $praticiens = $this->loadRefsPraticiens();
      $user_in_list_prat = array_key_exists($user->_id, $praticiens);

      // Test si un l'utilisateur est présent dans les logs
      $user_in_logs      = false;
      $this->loadLogs();

      foreach ($this->_ref_logs as $_log) {
        if ($user->_id == $_log->user_id) {
          $user_in_logs = true;
          break;
        }
      }
      $this->_vip = !$user_in_list_prat && !$user_in_logs;
    }
    if ($this->_vip) {
      CValue::setSession("patient_id", 0);
    }
  }

  /**
   * Calcul l'âge de l'assuré en années
   *
   * @param string $date Date de référence pour le calcul, maintenant si null
   *
   * @return int l'age de l'assuré en années
   */
  function evalAgeAssure($date = null) {
    $achieved = CMbDate::achievedDurations($this->assure_naissance, $date);
    return $this->_age_assure = $achieved["year"];
  }

  /**
   * Calcul l'âge du patient en mois
   *
   * @param string $date Date de référence pour le calcul, maintenant si null
   *
   * @return int l'age du patient en mois
   */
  function evalAgeMois($date = null){
    $achieved = CMbDate::achievedDurations($this->naissance, $date);
    return $achieved["month"];
  }

  /**
   * Calcul l'âge du patient en semaines
   */
  function evalAgeSemaines($date = null){
    $jours = $this->evalAgeJours($date);
    return intval($jours/7);
  }

  /**
   * Calcul l'âge du patient en jours
   */
  function evalAgeJours($date = null){
    $date = $date ? $date : mbDate();
    if (!$this->naissance || $this->naissance === "0000-00-00") {
      return 0;
    }
    return mbDaysRelative($this->naissance, $date);
  }

  function updatePlainFields() {
    parent::updatePlainFields();

    $soundex2 = new soundex2;
    if ($this->nom) {
      $this->nom = CMbString::upper($this->nom);
      $this->nom_soundex2 = $soundex2->build($this->nom);
    }

    if ($this->nom_jeune_fille) {
      $this->nom_jeune_fille = CMbString::upper($this->nom_jeune_fille);
      $this->nomjf_soundex2 = $soundex2->build($this->nom_jeune_fille);
    }

    if ($this->prenom) {
      $this->prenom = CMbString::capitalize(CMbString::lower($this->prenom));
      $this->prenom_soundex2 = $soundex2->build($this->prenom);
    }

    if ($this->cp === "00000") {
      $this->cp = "";
    }

    if ($this->assure_nom) {
      $this->assure_nom = CMbString::upper($this->assure_nom);
    }

    if ($this->assure_nom_jeune_fille) {
      $this->assure_nom_jeune_fille = CMbString::upper($this->assure_nom_jeune_fille);
    }

    if ($this->assure_prenom) {
      $this->assure_prenom = CMbString::capitalize(CMbString::lower($this->assure_prenom));
    }

    if ($this->assure_cp === "00000") {
      $this->assure_cp = "";
    }

    if ($this->_pays_naissance_insee) {
      $this->pays_naissance_insee = $this->updatePatNumPaysInsee($this->_pays_naissance_insee);
    }

    if ($this->pays) {
      $this->pays_insee = $this->updatePatNumPaysInsee($this->pays);
    }

    if ($this->_assure_pays_naissance_insee) {
      $this->assure_pays_naissance_insee = $this->updatePatNumPaysInsee($this->_assure_pays_naissance_insee);
    }

    if ($this->assure_pays) {
      $this->assure_pays_insee = $this->updatePatNumPaysInsee($this->assure_pays);
    }

    // Détermine la civilité du patient automatiquement (utile en cas d'import)
    $this->completeField("civilite");
    if ($this->civilite === "guess" || !$this->civilite) {
      $this->evalAge();
      $this->civilite = ($this->_annees < CAppUI::conf("dPpatients CPatient adult_age")) ?
        "enf" : (($this->sexe === "m") ? "m" : "mme");
    }

    // Détermine la civilité de l'assure automatiquement (utile en cas d'import)
    $this->completeField("assure_civilite");
    if ($this->assure_civilite === "guess" || !$this->assure_civilite) {
      $this->assure_naissance = mbDateFromLocale($this->assure_naissance);
      $this->evalAgeAssure();
      $sexe = $this->assure_sexe ? $this->assure_sexe : $this->sexe;
      $this->assure_civilite = ($this->_age_assure < CAppUI::conf("dPpatients CPatient adult_age")) ?
        "enf" : (($sexe === "m") ? "m" : "mme");
    }
  }

  // Backward references
  function loadRefsSejours($where = null) {
    if (!$this->_id) {
      return $this->_ref_sejours = array();
    }

    $sejour = new CSejour;
    if ($where === null) {
      $where = array();
    }

    $where["patient_id"] = "= '$this->_id'";
    $order = "entree DESC";
    return $this->_ref_sejours = $sejour->loadList($where, $order);
  }

  function getCurrSejour($dateTime = null) {
    if (!$dateTime) {
      $dateTime = mbDateTime();
    }

    $where[] = "'$dateTime' BETWEEN entree AND sortie";
    $this->loadRefsSejours($where);
  }

  /**
   * Get patient links
   */
  function loadPatientLinks() {
    if ($this->patient_link_id) {
      return $this->_ref_patient_links = array($this->loadFwdRef("patient_link_id"));
    }

    // return $this->_ref_patient_links = $this->loadBackRefs("patient_links");
  }

  /*
   * Get the next sejour from today or from a given date
   * @return array(CSejour, COperation);
   */
  function getNextSejourAndOperation($date = null, $withOperation = true) {
    $sejour = new CSejour;
    $op     = new COperation;
    if (!$date) {
      $date = mbDate();
    }
    if (!$this->_ref_sejours) {
      $this->loadRefsSejours();
    }
    foreach ($this->_ref_sejours as $_sejour) {
      if (in_array($_sejour->type, array("ambu", "comp", "exte")) && !$_sejour->annule && $_sejour->entree_prevue >= $date) {
        if (!$sejour->_id) {
          $sejour = $_sejour;
        }
        else {
          if ($_sejour->entree_prevue < $sejour->entree_prevue) {
            $sejour = $_sejour;
          }
        }

        if ($withOperation) {
          if (!$_sejour->_ref_operations) {
            $_sejour->loadRefsOperations(array("annulee" => "= '0'"));
          }
          foreach ($_sejour->_ref_operations as $_op) {
            $_op->loadRefPlageOp();
            if (!$op->_id) {
              $op = $_op;
            }
            else {
              if ($_op->_datetime < $op->_datetime) {
                $op = $_op;
              }
            }
          }
        }
      }
    }

    $sejour->loadRefPraticien()->loadRefFunction();
    $op->loadRefPraticien()->loadRefFunction();

    return array("CSejour" => $sejour, "COperation" => $op);
  }

  /**
   * Get an associative array of uncancelled sejours and their dates
   *
   * @return array Sejour ID => array("entree_prevue" => DATE, "sortie_prevue" => DATE)
   */
  function getSejoursCollisions() {
    $sejours_collision = array();
    $group_id = CGroups::loadCurrent()->_id;

    if ($this->_ref_sejours) {
      foreach ($this->_ref_sejours as $_sejour) {
        if (!$_sejour->annule && $_sejour->group_id == $group_id && !in_array($_sejour->type, array("urg", "seances", "consult"))) {
          $sejours_collision[$_sejour->_id] = array (
            "entree" => mbDate($_sejour->_entree),
            "sortie" => mbDate($_sejour->_sortie)
          );
        }
      }
    }

    return $sejours_collision;
  }

  /**
   * Charge le patient ayant les traits suivants :
   * - Même nom à la casse et aux séparateurs près
   * - Même prénom à la casse et aux séparateurs près
   * - Strictement la même date de naissance
   *
   * @param bool $other      Vérifier qu'on n'inclut pas $this
   * @param bool $loadObject Permet de ne pas charger le patient, seulement renvoyer le nombre de matches
   *
   * @return int Nombre d'occurences trouvées
   */
  function loadMatchingPatient($other = false, $loadObject = true) {
    $ds = $this->_spec->ds;

    if ($other && $this->_id) {
      $where["patient_id"] = " != '$this->_id'";
    }

    $whereOr[] = "nom "             . $ds->prepareLikeName($this->nom);
    $whereOr[] = "nom_jeune_fille " . $ds->prepareLikeName($this->nom);

    if ($this->nom_jeune_fille) {
      $whereOr[] = "nom "             . $ds->prepareLikeName($this->nom_jeune_fille);
      $whereOr[] = "nom_jeune_fille " . $ds->prepareLikeName($this->nom_jeune_fille);
    }

    $where[] = implode(" OR ", $whereOr);
    $where["prenom"]          = $ds->prepareLikeName($this->prenom);

    if ($this->prenom_2) $where["prenom_2"] = $ds->prepareLikeName($this->prenom_2);
    if ($this->prenom_3) $where["prenom_3"] = $ds->prepareLikeName($this->prenom_3);
    if ($this->prenom_4) $where["prenom_4"] = $ds->prepareLikeName($this->prenom_4);

    $where["naissance"] = $ds->prepare("= %", $this->naissance);

    if ($loadObject) {
      $this->loadObject($where);
    }

    return $this->countList($where);
  }

  /**
   * Finds patient siblings with at least two exact matching traits out of
   * nom, prenom, naissance
   * Optimized version with split queries for index usage forcing
   *
   * @return CPatient[] Array of siblings
   */
  function getSiblings() {
    $ds =& $this->_spec->ds;

    $where = array (
      "nom"    => $ds->prepareLikeName($this->nom),
      "prenom" => $ds->prepareLikeName($this->prenom),
      "patient_id" => "!= '$this->_id'",
    );

    $siblings = $this->loadList($where);

    if ($this->naissance !== "0000-00-00") {
      $where = array (
        "nom"       => $ds->prepareLikeName($this->nom),
        "naissance" => $ds->prepare(" = %", $this->naissance),
        "patient_id" => "!= '$this->_id'",
      );
      $siblings = CMbArray::mergeKeys($siblings, $this->loadList($where));

      $where = array (
        "prenom"    => $ds->prepareLikeName($this->prenom),
        "naissance" => $ds->prepare(" = %", $this->naissance),
        "patient_id" => "!= '$this->_id'",
      );
      $siblings = CMbArray::mergeKeys($siblings, $this->loadList($where));
    }

    return $siblings;
  }

  /**
   * Find patient phoning similar
   *
   * @param string $date restrict to a venue collide date
   *
   * @return CPatient[] Array of phoning patients
   */
  function getPhoning($date = null) {
    $whereNom[] = "nom_soundex2    LIKE '$this->nom_soundex2%'";
    $whereNom[] = "nomjf_soundex2  LIKE '$this->nom_soundex2%'";

    if ($this->nomjf_soundex2) {
      $whereNom[] = "nom_soundex2    LIKE '$this->nomjf_soundex2%'";
      $whereNom[] = "nomjf_soundex2  LIKE '$this->nomjf_soundex2%'";
    }

    $where[] = implode(" OR ", $whereNom);
    $where["prenom_soundex2"] = "LIKE '$this->prenom_soundex2%'";
    $where["patients.patient_id"] = "!= '$this->_id'";

    $join = null;
    if ($date) {
      $join["sejour"] = "sejour.patient_id = patients.patient_id";
      $min = mbDateTime("-1 DAY", $date);
      $max = mbDateTime("+1 DAY", $date);
      $whereDate[] = "sejour.entree_reelle BETWEEN '$min' AND '$max'";
      $whereDate[] = "sejour.entree_prevue BETWEEN '$min' AND '$max'";
      $where[] = implode(" OR ", $whereDate);
    }

    return $this->loadList($where, null, null, null, $join);
  }


  function checkSimilar($nom, $prenom, $strict = true) {
    $soundex2 = new soundex2;

    $testNom    = CMbString::lower($this->nom_soundex2)    == CMbString::lower($soundex2->build($nom));
    $testPrenom = CMbString::lower($this->prenom_soundex2) == CMbString::lower($soundex2->build($prenom));

    if ($strict) {
      return($testNom && $testPrenom);
    }
    else {
      return($testNom || $testPrenom);
    }
  }

  function loadRefsConsultations($where = null) {
    $consultation = new CConsultation();
    $curr_user = CAppUI::$user;
    if ($this->_id) {
      if ($where === null) {
        $where = array();
      }
      if (!$curr_user->isAdmin()) {
        $where[] = "functions_mediboard.consults_partagees = '1' ||
                    (functions_mediboard.consults_partagees = '0' && functions_mediboard.function_id = '$curr_user->function_id')";
      }
      $where["patient_id"] = "= '$this->_id'";
      $order = "plageconsult.date DESC";
      $leftjoin = array();
      $leftjoin["plageconsult"]        = "consultation.plageconsult_id = plageconsult.plageconsult_id";
      $leftjoin["users_mediboard"]     = "plageconsult.chir_id = users_mediboard.user_id";
      $leftjoin["functions_mediboard"] = "users_mediboard.function_id = functions_mediboard.function_id";
      return $this->_ref_consultations = $consultation->loadList($where, $order, null, null, $leftjoin);
    }

    return $this->_ref_consultations = array();
  }

  /**
   * @return CDossierMedical
   */
  function loadRefDossierMedical($load_refs_back = true) {
    $this->_ref_dossier_medical = $this->loadUniqueBackRef("dossier_medical");

    if ($load_refs_back) {
      $this->_ref_dossier_medical->loadRefsBack();
    }

    return $this->_ref_dossier_medical;
  }

  function loadRefsDevenirDentaire() {
    return $this->_refs_devenirs_dentaires = $this->loadBackRefs("devenirs_dentaires");
  }

  function loadRefsAffectations($date = null) {
    $affectation = new CAffectation();

    // Affectations inactives
    if (!$affectation->_ref_module) {
      $this->_ref_curr_affectation = null;
      $this->_ref_next_affectation = null;
    }

    if (!$date) {
      $date = mbDateTime();
    }

    $sejours = $this->loadRefsSejours();
    $group = CGroups::loadCurrent();

    // Affectation actuelle et prochaine affectation
    $where["affectation.sejour_id"] = CSQLDataSource::prepareIn(array_keys($sejours));
    $where["sejour.group_id"]       = "= '$group->_id'";
    $order = "affectation.entree";

    // @FIXME A quoi sert cette jointure ?
    $ljoin["sejour"]                = "sejour.sejour_id = affectation.sejour_id";

    // Affection courante
    $this->_ref_curr_affectation = new CAffectation();
    $where["affectation.entree"] = "<  '$date'";
    $where["affectation.sortie"] = ">= '$date'";
    $this->_ref_curr_affectation->loadObject($where, $order, null, $ljoin);

    // Prochaine affectations
    $this->_ref_next_affectation = new CAffectation();
    $where["affectation.entree"] = "> '$date'";
    $this->_ref_next_affectation->loadObject($where, $order, null, $ljoin);
  }

  function loadRefsDocs() {
    $docs_valid = parent::loadRefsDocs();
    if ($docs_valid) {
      $this->_nb_docs .= "$docs_valid";
    }
  }

  function loadRefsPrescriptions($perm = null) {
    if (CModule::getInstalled("dPlabo")) {
      $prescription = new CPrescriptionLabo();
      $where = array("patient_id" => "= '$this->_id'");
      $order = "date DESC";
      $this->_ref_prescriptions = $prescription->loadListWithPerms($perm, $where, $order);
    }
  }

  function loadRefConstantesMedicales($datetime = null, $selection = array()) {
    $latest = CConstantesMedicales::getLatestFor($this, $datetime, $selection);

    list($this->_ref_constantes_medicales, /*$dates*/) = $latest;
    $this->_ref_constantes_medicales->updateFormFields();

    return $latest;
  }

  function loadRefsGrossesses($order = "terme_prevu DESC") {
    return $this->_ref_grossesses = $this->loadBackRefs("grossesses", $order);
  }

  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsFiles();
    $this->loadRefsDocs();
    $this->loadRefsConsultations();
    $this->loadRefsCorrespondants();
    $this->loadRefsAffectations();
    $this->loadRefsPrescriptions();
    $this->loadRefsGrossesses();
  }

  function loadIdVitale() {
    if (CModule::getActive("fse")) {
      $cv = CFseFactory::createCV();
      if ($cv) {
        $cv->loadIdVitale($this);
      }
    }
  }

  // Forward references
  function loadRefsFwd() {
    $this->loadIdVitale();
  }

  function loadComplete(){
    parent::loadComplete();
    $this->loadIPP();
    $this->loadRefPhotoIdentite();
    $this->loadRefsCorrespondantsPatient();
    $this->loadRefDossierMedical();
    $this->_ref_dossier_medical->canRead();
    $this->_ref_dossier_medical->loadRefsAntecedents();
    $this->_ref_dossier_medical->loadRefsTraitements();
    $prescription = $this->_ref_dossier_medical->loadRefPrescription();

    if ($prescription && is_array($prescription->_ref_prescription_lines)) {
      foreach ($prescription->_ref_prescription_lines as $_line) {
        $_line->loadRefsPrises();
      }
    }
  }

  function loadRefsPraticiens() {
    // Consultations
    $this->loadRefsConsultations();
    foreach ($this->_ref_consultations as $_consult) {
      $praticien = $_consult->loadRefPraticien();
      $praticien->loadRefFunction()->loadRefGroup();
      $this->_ref_praticiens[$praticien->_id] = $praticien;
    }
    // Séjours
    $this->loadRefsSejours();
    foreach ($this->_ref_sejours as $_sejour) {
      $praticien = $_sejour->loadRefPraticien();
      $praticien->loadRefFunction()->loadRefGroup();
      $this->_ref_praticiens[$praticien->_id] = $praticien;
      $_sejour->loadRefsOperations();
      foreach ($_sejour->_ref_operations as $_operation) {
        $praticien = $_operation->loadRefPraticien();
        $praticien->loadRefFunction()->loadRefGroup();
        $this->_ref_praticiens[$praticien->_id] = $praticien;
      }
    }
    return $this->_ref_praticiens;
  }

  function loadDossierComplet($permType = null) {
    $this->_ref_praticiens = array();

    if (!$this->_id) {
      return;
    }

    // Patient permission
    $this->canRead();
    $this->canEdit();

    // Doc items
    $this->loadRefsFiles();
    $this->loadRefsDocs();
    $this->countDocItems($permType);

    // Photos et Notes
    $this->loadRefPhotoIdentite();
    $this->loadRefsNotes();

    // Correspondants
    $this->loadRefsCorrespondants();
    $this->loadRefsCorrespondantsPatient();

    // Affectations courantes
    $this->loadRefsAffectations();
    $affectation = $this->_ref_curr_affectation;
    if ($affectation && $affectation->_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }

    $affectation = $this->_ref_next_affectation;
    if ($affectation && $affectation->affectation_id) {
      $affectation->loadRefsFwd();
      $affectation->_ref_lit->loadCompleteView();
    }

    $maternite_active = CModule::getActive("maternite");
    if ($maternite_active) {
      $this->loadRefsGrossesses();
    }

    // Consultations
    $this->loadRefsConsultations();
    foreach ($this->_ref_consultations as $consult) {
      if ($consult->sejour_id) {
        unset($this->_ref_consultations[$consult->_id]);
        continue;
      }

      // Permission
      $consult->canRead();
      $consult->canEdit();

      $consult->loadRefConsultAnesth();
      $consult->loadRefsFichesExamen();
      $consult->loadExamsComp();
      $consult->countDocItems($permType);

      // Praticien
      $consult->getType();
      $praticien = $consult->_ref_praticien;
			
      $this->_ref_praticiens[$praticien->_id] = $praticien;
      $praticien->loadRefFunction()->loadRefGroup();

      $consult->loadRefConsultAnesth()->countDocItems();

      // Grossesse
      if ($maternite_active && $consult->grossesse_id) {
        $consult->_semaine_grossesse = ceil((mbDaysRelative($this->_ref_grossesses[$consult->grossesse_id]->_date_fecondation, $consult->_date))/7);
        $this->_ref_grossesses[$consult->grossesse_id]->_ref_consultations[$consult->_id] = $consult;
      }
    }

    // Sejours
    foreach ($this->_ref_sejours as $_sejour) {
      // Permission
      $_sejour->canRead();
      $_sejour->canEdit();

      //
      $_sejour->loadNDA();
      $_sejour->loadRefsAffectations();
      $_sejour->countDocItems($permType);

      // Praticien
      $praticien = $_sejour->loadRefPraticien(1);
      $this->_ref_praticiens[$praticien->_id] = $praticien;

      $_sejour->countDocItems($permType);
      if ($maternite_active && $_sejour->grossesse_id) {
        $this->_ref_grossesses[$_sejour->grossesse_id]->_ref_sejours[$_sejour->_id] = $_sejour;
      }

      $_sejour->loadRefsOperations();
      foreach ($_sejour->_ref_operations as $_operation) {
        $_operation->canRead();
        $_operation->canEdit();

        // Praticien
        $praticien = $_operation->loadRefPraticien(1);
        $praticien->loadRefFunction();
        $this->_ref_praticiens[$praticien->_id] = $praticien;

        // Autres
        $_operation->loadRefPlageOp(1);
        $_operation->countDocItems($permType);

        // Consultation d'anesthésie
        $consult_anesth = $_operation->loadRefsConsultAnesth();
        $consult_anesth->countDocItems();

        $consultation = $consult_anesth->loadRefConsultation();
        $consultation->countDocItems();
        $consultation->canRead();
        $consultation->canEdit();
      }

      // RPU
      $rpu = $_sejour->loadRefRPU();
      if ($rpu && $rpu->_id) {
        $rpu->countDocItems($permType);
      }

      $_sejour->loadRefsConsultations();
      foreach ($_sejour->_ref_consultations as $_consult) {
        $_consult->loadRefConsultAnesth();
        $_consult->loadRefsFichesExamen();
        $_consult->loadExamsComp();
        $_consult->countDocItems($permType);

        $_consult->loadRefsFwd(1);
        $_consult->_ref_sejour = $_sejour;
        $_consult->getType();
        $_consult->_ref_chir->loadRefFunction();
        $_consult->_ref_chir->_ref_function->loadRefGroup();
        $_consult->canRead();
        $_consult->canEdit();
      }
    }
  }

  function loadRefsCorrespondants() {
    // Médecin traitant
    $this->_ref_medecin_traitant = new CMedecin();
    $this->_ref_medecin_traitant->load($this->medecin_traitant);

    // Autres correspondant
    $this->_ref_medecins_correspondants = $this->loadBackRefs("correspondants");
    foreach ($this->_ref_medecins_correspondants as &$corresp) {
      $corresp->loadRefsFwd();
    }

    return $this->_ref_medecins_correspondants;
  }

  // Prévenir - Confiance - Employeur
  function loadRefsCorrespondantsPatient() {
    $this->_ref_correspondants_patient = $this->loadBackRefs("correspondants_patient");

    $correspondant = new CCorrespondantPatient;
    $this->_ref_cp_by_relation = array();
    foreach (explode("|", $correspondant->_specs["relation"]->list) as $_relation) {
      $this->_ref_cp_by_relation[$_relation] = array();
    }

    foreach ($this->_ref_correspondants_patient as $_correspondant) {
      $this->_ref_cp_by_relation[$_correspondant->relation][$_correspondant->_id] = $_correspondant;
    }

    return $this->_ref_correspondants_patient;
  }

  /**
   * Construit le tag IPP en fonction des variables de configuration
   *
   * @param int $group_id Permet de charger l'IPP pour un établissement donné si non null
   *
   * @return string
   */
  static function getTagIPP($group_id = null) {
    // Pas de tag IPP => pas d'affichage d'IPP
    if (null == $tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp")) {
      return;
    }

    // Permettre des IPP en fonction de l'établissement
    $group = CGroups::loadCurrent();
    if (!$group_id) {
      $group_id = $group->_id;
    }

    // Si on est dans le cas d'un établissement gérant la numérotation
    $group->loadConfigValues();
    if ($group->_configs["sip_idex_generator"]) {
      $tag_ipp = CAppUI::conf("sip tag_ipp");
    }

    // Préférer un identifiant externe de l'établissement
    if ($tag_group_idex = CAppUI::conf("dPpatients CPatient tag_ipp_group_idex")) {
      $idex = new CIdSante400();
      $idex->loadLatestFor($group, $tag_group_idex);
      $group_id = $idex->id400;
    }

    return str_replace('$g', $group_id, $tag_ipp);
  }

  /**
   * Charge l'IPP du patient pour l'établissement courant
   *
   * @param int $group_id Permet de charger l'IPP pour un établissement donné si non null
   *
   * @return void
   */
  function loadIPP($group_id = null) {
    if (!$this->_id) {
      return;
    }

    // Prevent loading twice
    if ($this->_IPP) {
      return;
    }

    // Pas de tag IPP => pas d'affichage d'IPP
    if (null == $tag_ipp = $this->getTagIPP($group_id)) {
      $this->_IPP = str_pad($this->_id, 6, "0", STR_PAD_LEFT);
      return;
    }

    // Récupération du premier IPP créé, utile pour la gestion des doublons
    $order = "id400 ASC";

    // Recuperation de la valeur de l'id400
    $idex = new CIdSante400();
    $idex->setObject($this);
    $idex->tag = $tag_ipp;
    $idex->loadMatchingObject($order);

    // Stockage de la valeur de l'id400
    $this->_ref_IPP = $idex;
    $this->_IPP     = $idex->id400;
  }

  function loadFromIPP($group_id = null) {
    if (!$this->_IPP) {
      return;
    }

    // Pas de tag IPP => pas d'affichage d'IPP
    if (null == $tag_ipp = $this->getTagIPP($group_id)) {
      return;
    }

    // Recuperation de la valeur de l'id400
    $idex = new CIdSante400();
    $idex->object_class= 'CPatient';
    $idex->tag = $tag_ipp;
    $idex->id400 = $this->_IPP;
    $idex->loadMatchingObject();

    $this->load($idex->object_id);
  }

  function loadRefPhotoIdentite() {
    $file = CFile::loadNamed($this, "identite.jpg");
    $this->_can_see_photo = 1;
    if ($file->_id) {
      $author = $file->loadRefAuthor();
      global $can;
      $this->_can_see_photo = $can->admin || CAppUI::$user->function_id == $author->function_id;
    }

    return $this->_ref_photo_identite = $file;
  }

  function loadLastGrossesse() {
    $grossesse = new CGrossesse;
    $grossesse->parturiente_id = $this->_id;
    $grossesse->loadMatchingObject("terme_prevu desc");
    return $this->_ref_last_grossesse = $grossesse;
  }

  function getTemplateClasses(){
    $tab = array();
    $tab['CPatient'] = $this->_id;
    $tab['CSejour'] = 0;
    $tab['COperation'] = 0;

    return $tab;
  }

  function getFirstConstantes() {
    $constantes = new CConstantesMedicales;
    $constantes->patient_id = $this->_id;
    $constantes->loadMatchingObject("datetime ASC");
    return $this->_ref_first_constantes = $constantes;
  }

  function fillLimitedTemplate(&$template) {
    CDestinataire::makeAllFor($this);

    $destinataires = CDestinataire::$destByClass;

    foreach ($destinataires as $_destinataires_by_class) {
      foreach ($_destinataires_by_class as $_destinataire) {
        if (!isset($_destinataire->nom) || strlen($_destinataire->nom) == 0 || $_destinataire->nom === " ") {
          continue;
        }

        $template->destinataires[] = array(
          "nom"   => $_destinataire->nom,
          "email" => $_destinataire->email,
          "tag"   => $_destinataire->tag,
        );
      }
    }

    $this->loadRefsFwd();
    $this->loadRefConstantesMedicales();
    $this->loadIPP();

    $this->notify("BeforeFillLimitedTemplate", $template);

    $template->addProperty("Patient - article"           , $this->_civilite  );
    $template->addProperty("Patient - article long"      , ucfirst($this->_civilite_long));
    $template->addProperty("Patient - article long (minuscule)", strtolower($this->_civilite_long));
    $template->addProperty("Patient - nom"               , $this->nom        );
    $template->addProperty("Patient - nom jeune fille"   , $this->nom_jeune_fille);
    $template->addProperty("Patient - prénom"            , $this->prenom     );
    $template->addProperty("Patient - adresse"           , $this->adresse    );
    $template->addProperty("Patient - ville"             , $this->ville      );
    $template->addProperty("Patient - cp"                , $this->cp         );
    $template->addProperty("Patient - années"            , $this->_annees    );
    $template->addProperty("Patient - âge"               , $this->_age       );
    $template->addDateProperty("Patient - date de naissance", $this->naissance);
    $template->addProperty("Patient - lieu de naissance" , $this->lieu_naissance);
    $template->addProperty("Patient - sexe"              , strtolower($this->getFormattedValue("sexe")));
    $template->addProperty("Patient - sexe court"        , substr(strtolower($this->getFormattedValue("sexe")), 0, 1));
    $template->addProperty("Patient - numéro d'assuré"   , $this->getFormattedValue("matricule"));
    $template->addProperty("Patient - téléphone"         , $this->getFormattedValue("tel"));
    $template->addProperty("Patient - mobile"            , $this->getFormattedValue("tel2"));
    $template->addProperty("Patient - téléphone autre"   , $this->tel_autre  );
    $template->addProperty("Patient - profession"        , $this->profession );
    $template->addProperty("Patient - IPP"               , $this->_IPP       );
    $template->addProperty("Patient - Qualité bénéficiaire", $this->qual_beneficiaire);

    $this->guessExoneration();
    $template->addProperty("Patient - Qualité bénéficiaire - Libellé", $this->libelle_exo);
    $template->addProperty("Patient - Numéro de sécurité sociale", $this->getFormattedValue("matricule"));
    $template->addBarcode ("Patient - Code barre ID"     , "PID$this->_id"   );
    $template->addBarcode ("Patient - Code barre IPP"    , "IPP$this->_IPP"  );

    if ($this->sexe === "m") {
      $template->addProperty("Patient - il/elle"         , "il"              );
      $template->addProperty("Patient - Il/Elle (majuscule)", "Il"           );
      $template->addProperty("Patient - le/la"           , "le"              );
      $template->addProperty("Patient - Le/La (majuscule)", "Le"             );
      $template->addProperty("Patient - du/de la"        , "du"              );
      $template->addProperty("Patient - accord genre"    , ""                );
    }
    else {
      $template->addProperty("Patient - il/elle"         , "elle"            );
      $template->addProperty("Patient - Il/Elle (majuscule)", "Elle"         );
      $template->addProperty("Patient - le/la"           , "la"              );
      $template->addProperty("Patient - Le/La (majuscule)", "La"             );
      $template->addProperty("Patient - du/de la"        , "de la"           );
      $template->addProperty("Patient - accord genre"    , "e"               );
    }

    if ($this->medecin_traitant) {
      $medecin = $this->_ref_medecin_traitant;
      $template->addProperty("Patient - médecin traitant"          , "$medecin->nom $medecin->prenom");
      $template->addProperty("Patient - médecin traitant - adresse", "$medecin->adresse \n $medecin->cp $medecin->ville");
    }
    else {
      $template->addProperty("Patient - médecin traitant");
      $template->addProperty("Patient - médecin traitant - adresse");
    }

    // Employeur
    $this->loadRefsCorrespondantsPatient();
    $correspondants = $this->_ref_cp_by_relation;

    foreach ($correspondants as $relation => $_correspondants) {
      $_correspondant = @reset($_correspondants);

      // Dans le cas d'un modèle, création d'un correspondant pour chaque type de relation
      if (!count($_correspondants)) {
        $_correspondant = new CCorrespondantPatient;
        $_correspondant->relation = $relation;
      }

      switch($_correspondant->relation) {
        case "employeur" :
          $template->addProperty("Patient - employeur - nom"    , $_correspondant->nom);
          $template->addProperty("Patient - employeur - adresse", $_correspondant->adresse);
          $template->addProperty("Patient - employeur - cp"     , $_correspondant->cp);
          $template->addProperty("Patient - employeur - ville"  , $_correspondant->ville);
          $template->addProperty("Patient - employeur - tel"    , $_correspondant->getFormattedValue("tel"));
          $template->addProperty("Patient - employeur - mobile" , $_correspondant->getFormattedValue("mob"));
          $template->addProperty("Patient - employeur - urssaf" , $_correspondant->urssaf);
          break;
        case "prevenir":
          $template->addProperty("Patient - prévenir - nom"    , $_correspondant->nom);
          $template->addProperty("Patient - prévenir - prénom" , $_correspondant->prenom);
          $template->addProperty("Patient - prévenir - adresse", $_correspondant->adresse);
          $template->addProperty("Patient - prévenir - cp"     , $_correspondant->cp);
          $template->addProperty("Patient - prévenir - ville"  , $_correspondant->ville);
          $template->addProperty("Patient - prévenir - tel"    , $_correspondant->getFormattedValue("tel"));
          $template->addProperty("Patient - prévenir - mobile" , $_correspondant->getFormattedValue("mob"));
          $template->addProperty("Patient - prévenir - parente", $_correspondant->parente);
          break;
        case "confiance":
          $template->addProperty("Patient - confiance - nom"    , $_correspondant->nom);
          $template->addProperty("Patient - confiance - nom de jeune fille" , $_correspondant->nom_jeune_fille);
          $template->addProperty("Patient - confiance - prénom" , $_correspondant->prenom);
          $template->addProperty("Patient - confiance - date de naissance" , $_correspondant->getFormattedValue("naissance"));
          $template->addProperty("Patient - confiance - adresse", $_correspondant->adresse);
          $template->addProperty("Patient - confiance - cp"     , $_correspondant->cp);
          $template->addProperty("Patient - confiance - ville"  , $_correspondant->ville);
          $template->addProperty("Patient - confiance - tel"    , $_correspondant->getFormattedValue("tel"));
          $template->addProperty("Patient - confiance - mobile" , $_correspondant->getFormattedValue("mob"));
          $template->addProperty("Patient - confiance - parente", $_correspondant->parente);
      }
    }

    // Vider les anciens holders
    for ($i = 1; $i < 4; $i++) {
      $template->addProperty("Patient - médecin correspondant $i");
      $template->addProperty("Patient - médecin correspondant $i - adresse");
      $template->addProperty("Patient - médecin correspondant $i - spécialité");
    }

    $this->loadRefsCorrespondants();
    $i = 0;
    $noms = array();
    foreach ($this->_ref_medecins_correspondants as $corresp) {
      $i++;
      $corresp->loadRefsFwd();
      $medecin = $corresp->_ref_medecin;
      $nom = "{$medecin->nom} {$medecin->prenom}";
      $noms[] = $nom;
      $template->addProperty("Patient - médecin correspondant $i", $nom);
      $template->addProperty("Patient - médecin correspondant $i - adresse", "{$medecin->adresse}\n{$medecin->cp} {$medecin->ville}");
      $template->addProperty("Patient - médecin correspondant $i - spécialité", htmlentities($medecin->disciplines));
    }

    $template->addProperty("Patient - médecins correspondants", implode(" - ", $noms));

    //Liste des séjours du patient
    $this->loadRefsSejours();

    if (is_array($this->_ref_sejours)) {
      foreach ($this->_ref_sejours as $_sejour) {
        $_sejour->loadRefPraticien();
      }
      $smarty = new CSmartyDP("modules/dPpatients");
      $smarty->assign("sejours", $this->_ref_sejours);
      $sejours = $smarty->fetch("print_closed_sejours.tpl",'','',0);
      $sejours = preg_replace('`([\\n\\r])`', '', $sejours);
    }
    else {
      $sejours = CAppUI::tr("CSejour.none");
    }
    $template->addProperty("Patient - liste des séjours", $sejours, '', false);

    $const_med = $this->_ref_constantes_medicales;

    $grid_complet = CConstantesMedicales::buildGrid(array($const_med), true);
    $grid_minimal = CConstantesMedicales::buildGrid(array($const_med), false);
    $grid_valued  = CConstantesMedicales::buildGrid(array($const_med), false, true);

    $smarty = new CSmartyDP("modules/dPpatients");

    // Horizontal
    $smarty->assign("constantes_medicales_grid", $grid_complet);
    $constantes_complet_horiz = $smarty->fetch("print_constantes.tpl",'','',0);
    $constantes_complet_horiz = preg_replace('`([\\n\\r])`', '', $constantes_complet_horiz);

    $smarty->assign("constantes_medicales_grid" , $grid_minimal);
    $constantes_minimal_horiz = $smarty->fetch("print_constantes.tpl",'','',0);
    $constantes_minimal_horiz = preg_replace('`([\\n\\r])`', '', $constantes_minimal_horiz);

    $smarty->assign("constantes_medicales_grid" , $grid_valued);
    $constantes_valued_horiz  = $smarty->fetch("print_constantes.tpl",'','',0);
    $constantes_valued_horiz  = preg_replace('`([\\n\\r])`', '', $constantes_valued_horiz);

    // Vertical
    $smarty->assign("constantes_medicales_grid", $grid_complet);
    $constantes_complet_vert  = $smarty->fetch("print_constantes_vert.tpl",'','',0);
    $constantes_complet_vert  = preg_replace('`([\\n\\r])`', '', $constantes_complet_vert);

    $smarty->assign("constantes_medicales_grid" , $grid_minimal);
    $constantes_minimal_vert  = $smarty->fetch("print_constantes_vert.tpl",'','',0);
    $constantes_minimal_vert  = preg_replace('`([\\n\\r])`', '', $constantes_minimal_vert);

    $smarty->assign("constantes_medicales_grid" , $grid_valued);
    $constantes_valued_vert   = $smarty->fetch("print_constantes_vert.tpl",'','',0);
    $constantes_valued_vert   = preg_replace('`([\\n\\r])`', '', $constantes_valued_vert);

    // Liste des fichiers
    $this->loadRefsFiles();
    $list = CMbArray::pluck($this->_ref_files, "file_name");
    $template->addListProperty("Patient - Liste des fichiers", $list);

    // Identité
    $identite = $this->loadNamedFile("identite.jpg");
    $template->addImageProperty("Patient - Photo d'identite", $identite->_id);

    $template->addProperty("Patient - Constantes - mode complet horizontal", $constantes_complet_horiz, '', false);
    $template->addProperty("Patient - Constantes - mode minimal horizontal", $constantes_minimal_horiz, '', false);
    $template->addProperty("Patient - Constantes - mode avec valeurs horizontal", $constantes_valued_horiz, '', false);
    $template->addProperty("Patient - Constantes - mode complet vertical"  , $constantes_complet_vert, '', false);
    $template->addProperty("Patient - Constantes - mode minimal vertical"  , $constantes_minimal_vert, '', false);
    $template->addProperty("Patient - Constantes - mode avec valeurs vertical"  , $constantes_valued_vert, '', false);
    $template->addProperty("Patient - poids",  "$const_med->poids kg");
    $template->addProperty("Patient - taille", "$const_med->taille cm");
    $template->addProperty("Patient - Pouls",  $const_med->pouls);
    $template->addProperty("Patient - IMC",    $const_med->_imc);
    $template->addProperty("Patient - VST",    $const_med->_vst);
    $template->addProperty("Patient - température", $const_med->temperature."°");
    $template->addProperty("Patient - TA",     ($const_med->ta ? "$const_med->_ta_systole / $const_med->_ta_diastole" : ""));

    // Assuré social
    $template->addProperty("Patient - Assuré social - nom", $this->assure_nom);
    $template->addProperty("Patient - Assuré social - nom jeune fille", $this->assure_nom_jeune_fille);
    $template->addProperty("Patient - Assuré social - prénom", $this->assure_prenom);
    $template->addDateProperty("Patient - Assuré social - date de naissance", $this->assure_naissance);
    $template->addProperty("Patient - Assuré social - article", $this->_assure_civilite);
    $template->addProperty("Patient - Assuré social - article long", $this->_assure_civilite_long);
    $template->addProperty("Patient - Assuré social - adresse", $this->assure_adresse);
    $template->addProperty("Patient - Assuré social - ville", $this->assure_ville);
    $template->addProperty("Patient - Assuré social - cp", $this->assure_cp);
    $template->addProperty("Patient - Assuré social - pays", $this->assure_pays);
    $template->addProperty("Patient - Assuré social - téléphone", $this->assure_tel);
    $template->addProperty("Patient - Assuré social - mobile", $this->assure_tel2);
    $template->addProperty("Patient - Assuré social - cp naissance", $this->assure_cp_naissance);
    $template->addProperty("Patient - Assuré social - lieu de naissance", $this->assure_lieu_naissance);
    $template->addProperty("Patient - Assuré social - profession", $this->assure_profession);

    // Bénéficiaire de soins
    $template->addProperty("Patient - Bénéficiaire de soin - code régime", $this->code_regime);
    $template->addProperty("Patient - Bénéficiaire de soin - caisse gest", $this->caisse_gest);
    $template->addProperty("Patient - Bénéficiaire de soin - centre gest", $this->centre_gest);
    $template->addProperty("Patient - Bénéficiaire de soin - code gest"  , $this->code_gestion);
    $template->addProperty("Patient - Bénéficiaire de soin - régime santé", $this->regime_sante);
    $template->addDateProperty("Patient - Bénéficiaire de soin - début période", $this->deb_amo);
    $template->addDateProperty("Patient - Bénéficiaire de soin - fin période", $this->fin_amo);
    $template->addProperty("Patient - Bénéficiaire de soin - régime am"  , $this->getFormattedValue("regime_am"));
    $template->addProperty("Patient - Bénéficiaire de soin - ald"        , $this->getFormattedValue("ald"));
    $template->addProperty("Patient - Bénéficiaire de soin - incapable majeur", $this->getFormattedValue("incapable_majeur"));
    $template->addProperty("Patient - Bénéficiaire de soin - cmu"        , $this->getFormattedValue("cmu"));
    $template->addProperty("Patient - Bénéficiaire de soin - ATNC"       , $this->getFormattedValue("ATNC"));
    $template->addDateProperty("Patient - Bénéficiaire de soin - validité vitale", $this->fin_validite_vitale);
    $template->addProperty("Patient - Bénéficiaire de soin - médecin traitant déclaré", $this->getFormattedValue("medecin_traitant_declare"));
    $template->addProperty("Patient - Bénéficiaire de soin - types contrat mutuelle", addslashes($this->mutuelle_types_contrat));
    $template->addProperty("Patient - Bénéficiaire de soin - notes amo"  , addslashes($this->notes_amo));
    $template->addProperty("Patient - Bénéficiaire de soin - libellé exo", addslashes($this->libelle_exo));
    $template->addProperty("Patient - Bénéficiaire de soin - notes amc"  , addslashes($this->notes_amc));

    $this->notify("AfterFillLimitedTemplate", $template);
  }

  function fillTemplate(&$template) {
    $this->fillLimitedTemplate($template);

    // Dossier médical
    $this->loadRefDossierMedical();
    $this->_ref_dossier_medical->fillTemplate($template);
  }

  function getLabelTable() {
    return array (
      "[NOM]"        => $this->nom,
      "[PRENOM]"     => $this->prenom,
      "[SEXE]"       => $this->sexe,
      "[NOM JF]"     => $this->nom_jeune_fille,
      "[DATE NAISS]" => $this->naissance,
      "[NUM SECU]"   => $this->matricule,
    );
  }

  function updateNomPaysInsee() {
    $pays = new CPaysInsee();
    if ($this->pays_naissance_insee) {
      $where = array(
        "numerique" => $pays->_spec->ds->prepare("= %", $this->pays_naissance_insee),
      );
      $pays->loadObject($where);
      $this->_pays_naissance_insee = $pays->nom_fr;
    }
    if ($this->assure_pays_naissance_insee) {
      $where = array(
        "numerique" => $pays->_spec->ds->prepare("= %", $this->assure_pays_naissance_insee),
      );
      $pays->loadObject($where);
      $this->_assure_pays_naissance_insee = $pays->nom_fr;
    }
  }

  function updatePatNomPays($pays_insee) {
    $pays = new CPaysInsee();
    $where = array(
      "numerique" => $pays->_spec->ds->prepare("= %", $pays_insee),
    );
    $pays->loadObject($where);

    return $pays->nom_fr;
  }

  function getCSPName() {
    // Query
    $select = "SELECT LIBELLE FROM categorie_socioprofessionnelle";
    $where  = "WHERE CODE = '$this->csp'";
    $query  = "$select $where";

    $ds = CSQLDataSource::get("INSEE");

    return $ds->loadResult($query);
  }

  function updatePatNumPaysInsee($nomPays) {
    $pays = new CPaysInsee();
    $pays->nom_fr = $this->_spec->ds->escape($nomPays);
    $pays->loadMatchingObject();
    return $pays->numerique;
  }

  function checkAnonymous() {
    return $this->nom === "ANONYME" && $this->prenom === "Anonyme";
  }

  function toVcard(CMbvCardExport $vcard) {
    $vcard->addName($this->prenom, $this->nom, ucfirst($this->civilite));
    $vcard->addBirthDate($this->naissance);
    $vcard->addPhoneNumber($this->tel, 'HOME');
    $vcard->addPhoneNumber($this->tel2, 'CELL');
    $vcard->addPhoneNumber($this->tel_autre, 'WORK');
    $vcard->addEmail($this->email);
    $vcard->addAddress($this->adresse, $this->ville, $this->cp, $this->pays, 'HOME');
    $vcard->addTitle(ucfirst($this->profession));

    $this->loadRefPhotoIdentite();
    if ($this->_ref_photo_identite->_id) {
      $vcard->addPicture($this->_ref_photo_identite);
    }
  }

  function isIPPConflict($ipp) {
    // Pas de tag IPP => pas d'affichage d'IPP
    if (null == $tag_ipp = CAppUI::conf("dPpatients CPatient tag_ipp")) {
      return;
    }

    $idex = new CIdSante400();
    $idex->object_class= 'CPatient';
    $idex->tag = $tag_ipp;
    $idex->id400 = $ipp;
    $idex->loadMatchingObject();

    return $idex->_id;
  }

  function countMatchingPatients() {
    $ds = CSQLDataSource::get("std");

    $res = $ds->query("SELECT COUNT(*) AS total,
      CONVERT( GROUP_CONCAT(`patient_id` SEPARATOR '|') USING latin1 ) AS ids ,
      LOWER( CONCAT_WS( '-',
        REPLACE( REPLACE( REPLACE( REPLACE( `nom` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) ,
        REPLACE( REPLACE( REPLACE( REPLACE( `prenom` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) ,
        `naissance`
        , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `nom_jeune_fille` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
        , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `prenom_2` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
        , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `prenom_3` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
        , QUOTE( REPLACE( REPLACE( REPLACE( REPLACE( `prenom_4` , '\\\\', '' ) , \"'\", '' ) , '-', '' ) , ' ', '' ) )
      )) AS hash
      FROM `patients`
      GROUP BY hash
      HAVING total > 1");

    return intval($ds->numRows($res));
  }

  function loadView() {
    parent::loadView();
    $this->loadIPP();
    $this->loadRefPhotoIdentite();
    $this->loadRefsCorrespondants();
  }

  function countNbEnfants() {
    $this->_nb_enfants = 0;
    foreach ($this->loadRefsGrossesses() as $_grossesse) {
      $this->_nb_enfants += $_grossesse->countBackRefs("naissances");
    }
    return $this->_nb_enfants;
  }

  function completeLabelFields(&$fields) {
    $this->loadIPP();
    $medecin_traitant = new CMedecin();
    $medecin_traitant->load($this->medecin_traitant);

    $fields = array_merge(
      $fields,
      array(
        "DATE NAISS"     => mbDateToLocale($this->naissance), "IPP"    => $this->_IPP,
        "LIEU NAISSANCE" => $this->lieu_naissance,
        "NOM"            => $this->nom,       "NOM JF" => $this->nom_jeune_fille,
        "NUM SECU"       => $this->matricule, "PRENOM" => $this->prenom,
        "SEXE"           => $this->sexe, "CIVILITE" => $this->civilite,
        "CIVILITE LONGUE" => $this->_civilite_long, "ACCORD GENRE" => $this->sexe == "f" ? "e" : "",
        "CODE BARRE IPP" => "@BARCODE_" . $this->_IPP."@",
        "ADRESSE"        => "$this->adresse \n$this->cp $this->ville",
        "MED. TRAITANT"  => "Dr $medecin_traitant->nom $medecin_traitant->prenom",
      )
    );
  }

  function docsEditable() {
    return true;
  }

  function calculINS_C($prenom=null, $naissance=null, $matricule=null) {
    // Fonction non implémentée avant php <= 5.1
    if (!function_exists("hash")) return;

    $norm = $prenom;

    if ($norm !== null) {
      if (!$norm) {
        $norm=" ";
      }
    }
    else {
      $norm = $this->prenom;

      if ($this->prenom_2) {
        $norm .= " " . $this->prenom_2;
      }
      if ($this->prenom_3) {
        $norm .= " " . $this->prenom_3;
      }
      if ($this->prenom_4) {
        $norm .= " " . $this->prenom_4;
      }
      if (!$norm) {
        $norm = " ";
      }
    }

    // Normalisation des caractères
    // Remplace les caractères accentués et spéciaux
    $norm = CMbString::removeDiacritics($norm);
    $norm = strtr($norm, "ÆæÐðÝýyßŠšŸ",
                         "AADDYYYBSSY");
    // Remplace les caractères confus (3 relevés 'NBSP','(c)'et'(r)') par un espace
    $norm = preg_replace(array("/NBSP/","/\(c\)/","/\(r\)/")," ", $norm);
    // Remplace tous les autres caractères par un espace
    $norm = preg_replace("/([^A-Za-z])/"," ", $norm);
    // Change les minuscules en majuscules
    $norm = mb_strtoupper($norm);

    /*...supprimer les espaces du prénom
     * retenir les 10 premiers caractères
     * tester si <10 ajouter des espaces
     * .....*/
    $norm = str_replace(" ", "", $norm);

    if (strlen($norm) < 10) {
      $norm = str_pad($norm, 10);
    }

    if (strlen($norm) > 10) {
      $norm = substr($norm, 0, 10);
    }

    // check birthdate
    $birthdate = $naissance;
    if ($birthdate === null) {
      $birthdate = mbTransformTime($this->naissance, null, "%d/%m/%Y");
    }

    if (!$birthdate) {
      $birthdate="000000";
    }
    elseif (preg_match("/^([0-3][0-9]\/[0-1][0-9]\/[1-2][0-9]{3})$/i", $birthdate)) {
      $a = substr($birthdate, 6, 4); // conversion
      $m = substr($birthdate, 3, 2); // de la date
      $j = substr($birthdate, 0, 2);
      $birthdate = $a.$m.$j;
      $birthdate = substr($birthdate, 2, 6);
    }
    elseif (preg_match("/^([1-2][0-9]{3}[0-1][0-9][0-3][0-9][0]{4})$/i", $birthdate)) {
      $birthdate = substr($birthdate, 2, 6);
    }
    elseif (preg_match("/^([0-9]{2}[0-1][0-9][0-3][0-9])$/i", $birthdate)) { // bon format
    }
    else {
      return "date de naissance non valide";
    }

    // Check $nir
    // Contrôler avec la clef
    $nir = $matricule;
    if ($nir === null) {
      $nir = $this->matricule;
    }

    if (preg_match("/^([0-9]{7,8}[A-Z])$/i", $nir)) {
      return "Matricule incomplet";
    }

    $matches = null;
    if (!preg_match("/^([12478][0-9]{2}[0-9]{2}[0-9][0-9ab][0-9]{3}[0-9]{3})([0-9]{2})$/i", $nir, $matches)) {
      return "Matricule incorrect";
    }

    $code = preg_replace(array('/2A/i', '/2B/i'), array(19, 18), $matches[1]);
    $cle  = $matches[2];

    if (97 - bcmod($code, 97) != $cle) {
      return "Matricule incorrect, la clé n'est pas valide";
    }

    // Création de la graine
    $seed = $norm.$birthdate.$code;

    // Hash de la graine
    $hash_seed = hash("sha256", $seed);

    // Calcul de l'INS-C
    $sha_hex = substr($hash_seed, 0, 16);
    $sha_dec = CPatient::bchexdec($sha_hex);
    $sha_dec = explode(".", $sha_dec);
    $sha_dec = $sha_dec[0];

    if (strlen($sha_dec) < 20) {
      $sha_dec = str_pad($sha_dec, 20, "0", STR_PAD_LEFT);
    }

    $cle = 97 - bcmod($sha_dec, 97);

    if (strlen($cle)<2) {
      $cle = str_pad($cle, 2, "0", STR_PAD_LEFT);
    }

    if ($prenom !== null && $naissance !== null && $matricule !== null) {
      return array(
        "prenom_norm" => $norm,
        "naissance_norm" => $birthdate,
        "seed" => $seed,
        "hash_seed" => $hash_seed,
        "insc" => $sha_dec,
        "cle_insc" => $cle
      );
    }
    else {
      $this->INSC = $sha_dec . $cle;
      $this->INSC_date = mbDateTime();
    }
  }

  function bchexdec($hex) {
    $dec = 0;
    $len = strlen($hex);
    for ($i = 1; $i <= $len; $i++) {
      $dec = bcadd($dec, bcmul(hexdec($hex[$i - 1]), bcpow('16', $len - $i)));
    }
    return $dec;
  }

  function getIncrementVars() {
    return array();
  }
}
