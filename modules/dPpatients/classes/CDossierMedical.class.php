<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Dossier Médical liés aux notions d'antécédents, traitements et diagnostics
 */
class CDossierMedical extends CMbMetaObject {
  // DB Fields
  public $dossier_medical_id;
  public $codes_cim;
  
  // Dossier medical Patient
  public $risque_thrombo_patient;
  public $risque_MCJ_patient;
  public $facteurs_risque;
  public $absence_traitement;

  // Dossier medical Sejour
  public $risque_thrombo_chirurgie;
  public $risque_antibioprophylaxie;
  public $risque_prophylaxie;
  public $risque_MCJ_chirurgie;

  public $groupe_sanguin;
  public $rhesus;
  public $groupe_ok;

  // Form Fields
  public $_added_code_cim;
  public $_deleted_code_cim;
  public $_codes_cim;
  public $_ext_codes_cim;

  // Back references
  /** @var  CAntecedent[] */
  public $_all_antecedents;
  public $_ref_antecedents_by_type;
  public $_ref_antecedents_by_appareil;
  public $_ref_antecedents_by_type_appareil;
  /** @var  CTraitement[] */
  public $_ref_traitements;
  public $_ref_etats_dents;
  /** @var  CPrescription */
  public $_ref_prescription;
  public $_ref_allergies;
  public $_ref_deficiences;
  
  // Derived back references
  public $_count_antecedents;
  public $_count_antecedents_by_type;
  public $_count_traitements;
  public $_count_cancelled_antecedents;
  public $_count_cancelled_traitements;

  public $_count_allergies;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'dossier_medical';
    $spec->key   = 'dossier_medical_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_class"]   = "enum list|CPatient|CSejour";
    $props["codes_cim"]      = "text";
    $props["absence_traitement"] = "bool";

    $props["groupe_sanguin"]  = "enum list|?|O|A|B|AB default|? show|0";
    $props["rhesus"]          = "enum list|?|NEG|POS default|? show|0";
    $props["groupe_ok"]       = "bool default|0 show|0";

    $props["risque_thrombo_patient"   ] = "enum list|NR|faible|modere|eleve|majeur default|NR";
    $props["risque_thrombo_chirurgie" ] = "enum list|NR|faible|modere|eleve default|NR";
    $props["risque_MCJ_patient"       ] = "enum list|NR|sans|avec|suspect|atteint default|NR";
    $props["risque_MCJ_chirurgie"     ] = "enum list|NR|sans|avec default|NR";
    $props["risque_antibioprophylaxie"] = "enum list|NR|non|oui default|NR";
    $props["risque_prophylaxie"       ] = "enum list|NR|non|oui default|NR";
    $props["facteurs_risque"]           = "text helped";
    return $props;
  }  

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["antecedents"] = "CAntecedent dossier_medical_id";
    $backProps["traitements"] = "CTraitement dossier_medical_id";
    $backProps["etats_dent"]  = "CEtatDent dossier_medical_id";
    $backProps["prescription"] = "CPrescription object_id";
    return $backProps;
  }

  /**
   * @see parent::getPerm()
   */
  function getPerm($permType) {
    $basePerm = CModule::getCanDo('soins')->edit      ||
                CModule::getCanDo('dPurgences')->edit ||
                CModule::getCanDo('dPcabinet')->edit  ||
                CModule::getCanDo('dPbloc')->edit     ||
                CModule::getCanDo('dPplanningOp')->edit;
    return $basePerm && parent::getPerm($permType);
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsAntecedents();
    $this->loadRefsTraitements();
  }

  /**
   * Chargement de la prescription du dossier médical
   *
   * @return CPrescription
   */
  function loadRefPrescription() {
    /** @var CPrescription $prescription */
    $prescription = $this->loadUniqueBackRef("prescription");

    if ($prescription && $prescription->_id) {
      $prescription->loadRefsLinesMed();
      $prescription->loadRefsLinesElement();
    }

    return $this->_ref_prescription = $prescription;
  }

  /**
   * Chargement de l'objet lié au dossier médical
   *
   * @return void
   */
  function loadRefObject() {
    $this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id);
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    // Tokens CIM
    $this->codes_cim = strtoupper($this->codes_cim);
    $this->_codes_cim = $this->codes_cim ? explode("|", $this->codes_cim) : array();

    // Objets CIM
    $this->_ext_codes_cim = array();
    foreach ($this->_codes_cim as $code_cim) {
      $this->_ext_codes_cim[$code_cim] = CCodeCIM10::get($code_cim);
    }
  }

  /**
   * @see parent::mergePlainFields()
   */
  function mergePlainFields($objects /*array(<CMbObject>)*/, $getFirstValue = false) {
    $codes_cim_array = CMbArray::pluck($objects, 'codes_cim');
    $codes_cim_array[] = $this->codes_cim;
    $codes_cim = implode('|', $codes_cim_array);
    $codes_cim_array = array_unique(explode('|', $codes_cim));
    CMbArray::removeValue('', $codes_cim_array);

    foreach ($objects as $objet) {
      if ($this->risque_thrombo_patient == 'NR') {
        $this->risque_thrombo_patient = $objet->risque_thrombo_patient;
      }
      if ($this->risque_MCJ_patient == 'NR') {
        $this->risque_MCJ_patient = $objet->risque_MCJ_patient;
      }
      if ($this->risque_thrombo_chirurgie == 'NR') {
        $this->risque_thrombo_chirurgie = $objet->risque_thrombo_chirurgie;
      }
      if ($this->risque_antibioprophylaxie == 'NR') {
        $this->risque_antibioprophylaxie = $objet->risque_antibioprophylaxie;
      }
      if ($this->risque_prophylaxie == 'NR') {
        $this->risque_prophylaxie = $objet->risque_prophylaxie;
      }
      if ($this->risque_MCJ_chirurgie == 'NR') {
        $this->risque_MCJ_chirurgie = $objet->risque_MCJ_chirurgie;
      }
      if (!$this->facteurs_risque) {
        $this->facteurs_risque = $objet->facteurs_risque;
      }
      if (!$this->absence_traitement) {
        $this->absence_traitement = $objet->absence_traitement;
      }
      if ($this->groupe_sanguin == '?') {
        $this->groupe_sanguin = $objet->groupe_sanguin;
      }
      if ($this->rhesus == '?') {
        $this->rhesus = $objet->rhesus  ;
      }
      if (!$this->groupe_ok) {
        $this->groupe_ok = $objet->groupe_ok;
      }
    }
    parent::mergePlainFields($objects);
    
    $this->codes_cim = implode('|', $codes_cim_array);
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();
    $this->loadComplete();
  }

  /**
   * @see parent::loadComplete()
   */
  function loadComplete() {
    parent::loadComplete();

    $this->loadRefsTraitements();
    $this->loadRefsAntecedents();
    $prescription = $this->loadRefPrescription();

    if ($prescription && is_array($prescription->_ref_prescription_lines)) {
      foreach ($prescription->_ref_prescription_lines as $_line) {
        $_line->loadRefsPrises();
      }
    }
  }

  /**
   * count Antecedents for type array
   *
   * @param array $types list of antecedent (array of string)
   *
   * @return int
   */
  function countRefsAntecedentsByType($types = null) {
    if (!$types) {
      $types = CAntecedent::$types;
    }
    $antecedent = new CAntecedent();
    $where = array();
    $where["type"] = $antecedent->getDS()->prepareIn($types);
    $where["dossier_medical_id"] = " = '$this->_id' ";
    $antecedent->dossier_medical_id = $this->_id;
    return $antecedent->countList($where);
  }

  /**
   * Chargement des antécédents du dossier
   *
   * @param bool $cancelled Prise en compte des annulés
   * @param bool $bydate    Sort by date the list
   *
   * @return CStoredObject[]|null
   */
  function loadRefsAntecedents($cancelled = false, $bydate = false) {
    // Initialisation du classement
    $order = $bydate ? "date DESC" : "CAST(type AS CHAR), CAST(appareil AS CHAR), rques";
    if (null === $this->_all_antecedents = $this->loadBackRefs("antecedents", $order)) {
      return null;
    }

    // Filtrage sur les annulés
    foreach ($this->_all_antecedents as $_atcd) {
      /** @var $_atcd CAntecedent */
      if ($_atcd->annule && !$cancelled) {
         unset($this->_all_antecedents[$_atcd->_id]);
      }
    }

    $atcd = new CAntecedent();

    // Classement par type
    $this->_ref_antecedents_by_type = array_fill_keys($atcd->_specs["type"]->_list, array());
    ksort($this->_ref_antecedents_by_type);
    foreach ($this->_all_antecedents as $_atcd) {
      $this->_ref_antecedents_by_type[$_atcd->type][$_atcd->_id] = $_atcd;
    }

    // Classement par appareil
    $this->_ref_antecedents_by_appareil = array_fill_keys($atcd->_specs["appareil"]->_list, array());
    foreach ($this->_all_antecedents as $_atcd) {
      $this->_ref_antecedents_by_appareil[$_atcd->appareil][$_atcd->_id] = $_atcd;
    }

    // Classement par type puis appareil
    $this->_ref_antecedents_by_type_appareil = array_fill_keys($atcd->_specs["type"]->_list, array());
    foreach ($this->_all_antecedents as $_atcd) {
      @$this->_ref_antecedents_by_type_appareil[$_atcd->type][$_atcd->appareil][$_atcd->_id] = $_atcd;
    }

    return $this->_all_antecedents;
  }

  /**
   * Chargement de l'état des dents
   *
   * @return CEtatDent[]
   */
  function loadRefsEtatsDents() {
    return $this->_ref_etats_dents = $this->loadBackRefs("etats_dent");
  }

  /**
   * Compte les antécédents annulés et non-annulés
   *
   * @param boolean $count_allergies Permet de préciser si les allergies sont prises en compte ou non
   *
   * @return void
   */
  function countAntecedents($count_allergies = true) {
    $antedecent = new CAntecedent();
    $where = array();
    $where["dossier_medical_id"] = " = '$this->_id'";
    $where["annule"] = " != '1'";
    if (!$count_allergies) {
      $where["type"] = " != 'alle' OR `type` IS NULL";
    }
    $this->_count_antecedents = $antedecent->countList($where);

    $where["annule"] = " = '1'";
    $this->_count_cancelled_antecedents = $antedecent->countList($where);
  }

  /**
   * MassCount des antecedents
   *
   * @param array   $dossiers        Dossier médicaux
   * @param boolean $count_allergies Permet de préciser si les allergies sont prises en compte ou non
   *
   * @return array
   */
  static function massCountAntecedents($dossiers = array(), $count_allergies = true) {
    $antecedent = new CAntecedent();
    $where = array();
    $where["annule"] = " != '1'";
    if (!$count_allergies) {
      $where["type"] = " != 'alle'";
    }
    $where["dossier_medical_id"] = CSQLDataSource::prepareIn($dossiers);

    $request = new CRequest();
    $request->addTable("antecedent");
    $request->addColumn("dossier_medical_id");
    $request->addColumn("count(*)", "c");
    $request->addWhere($where);
    $request->addGroup("dossier_medical_id");
    return $antecedent->getDS()->loadHashList($request->makeSelect());
  }

  /**
   * Compte les antécédents annulés et non-annulés
   * 
   * @return void
   */
  function countTraitements(){
    $traitement = new CTraitement();
    $where = array();
    $where["dossier_medical_id"] = " = '$this->_id'";

    $where["annule"] = " != '1'";
    $this->_count_traitements = $traitement->countList($where);

    $where["annule"] = " = '1'";
    $this->_count_cancelled_traitements = $traitement->countList($where);
  }
  
  /**
   * Compte les antecedents de type allergies
   * tout en tenant compte de la config pour ignorer certaines allergies
   *
   * @return int
   */
  function countAllergies(){
    if (!$this->_id) {
      return $this->_count_allergies = 0;
    }
    
    $antecedent = new CAntecedent();
    $where["type"] = "= 'alle'";
    $where["annule"] = " ='0'";
    $where["dossier_medical_id"] = " = '$this->_id'";
    $where["rques"] = 'NOT IN ("'.str_replace('|', '","', CAppUI::conf("soins Other ignore_allergies", CGroups::loadCurrent()->_guid)) . '")';
    
    return $this->_count_allergies = $antecedent->countList($where);
  }


  /**
   * MassCount des allergies
   *
   * @param array $dossiers Dossier médicaux
   *
   * @return array
   */
  static function massCountAllergies($dossiers = array()) {
    $antecedent = new CAntecedent();
    $where["type"] = "= 'alle'";
    $where["annule"] = " ='0'";
    $where["dossier_medical_id"] = CSQLDataSource::prepareIn($dossiers);
    $where["rques"] = 'NOT IN ("'.str_replace('|', '","', CAppUI::conf("soins Other ignore_allergies", CGroups::loadCurrent()->_guid)) . '")';

    $request = new CRequest();
    $request->addColumn("dossier_medical_id");
    $request->addColumn("count(*)", "c");
    $request->addWhere($where);
    $request->addGroup("dossier_medical_id");
    $request->addTable("antecedent");

    return $antecedent->getDS()->loadHashList($request->makeSelect());
  }


  /**
   * Chargmeent des antécédents par type
   *
   * @param string $type Type des antécédents
   *
   * @return array|CStoredObject[]
   */
  function loadRefsAntecedentsOfType($type) {
    if (!$this->_id) {
      return $this->_ref_antecedents_by_type[$type] = array();
    }
    
    $antecedent = new CAntecedent();
    $antecedent->type = $type;
    $antecedent->annule = "0";
    $antecedent->dossier_medical_id = $this->_id;
    return $this->_ref_antecedents_by_type[$type] = $antecedent->loadMatchingList();
  }

  /**
   * Chargement des allergies
   *
   * @return CAntecedent[]
   */
  function loadRefsAllergies(){
    return $this->_ref_allergies = $this->loadRefsAntecedentsOfType("alle");
  }

  /**
   * load the allergies not matching "ignore_allergies" (config)
   *
   * @return CAntecedent[] allergie list
   */
  function loadRefsActiveAllergies() {
    self::loadRefsAllergies();
    $allergies = array();
    $ignores = array_map('trim', explode("|", CAppUI::conf("soins Other ignore_allergies", CGroups::loadCurrent()->_guid)));
    foreach ($this->_ref_allergies as $_allergie) {
      if (!in_array(trim($_allergie->rques), $ignores)) {
        $allergies[] = $_allergie;
      }
    }
    return $this->_ref_allergies = $allergies;
  }

  /**
   * Chargement des déficiences
   *
   * @return CStoredObject[]
   */
  function loadRefsDeficiences(){
    return $this->_ref_deficiences = $this->loadRefsAntecedentsOfType("deficience");
  }

  /**
   * Comptage des antécédents par type
   *
   * @param self[] $dossiers liste des dossiers
   * @param string $type     Type des antécédents
   *
   * @return void
   */
  static function massCountAntecedentsByType($dossiers, $type = "") {
    if ($type && !preg_match("/$type/", CAppUI::conf("patients CAntecedent types"))) {
      return;
    }
    $where = array();
    if ($type) {
      $where["type"] = "= '$type'";
    }

    CMbObject::massCountBackRefs($dossiers, "antecedents", $where);

    foreach ($dossiers as $_dossier) {
      if ($type) {
        $_dossier->_count_antecedents_by_type[$type] = $_dossier->_count["antecedents"];
      }
      else {
        $_dossier->_count_antecedents = $_dossier->_count["antecedents"];
      }
    }
  }

  /**
   * Chargement des traitements personnels
   *
   * @param bool $cancelled Prise en compte des annulés
   *
   * @return CTraitement[]
   */
  function loadRefsTraitements($cancelled = false) {
    $order = "fin DESC, debut DESC";
    
    $this->_ref_traitements = $this->loadBackRefs("traitements", $order);
    
     // Filtrage sur les annulés
    foreach ($this->_ref_traitements as $_traitement) {
      /** @var $_traitement CTraitement */
      if ($_traitement->annule && !$cancelled) {
        unset($this->_ref_traitements[$_traitement->_id]);
      }
    }

    return $this->_ref_traitements;
  }
  
  /**
   * Identifiant de dossier médical lié à l'objet fourni. 
   * Crée le dossier médical si nécessaire
   *
   * @param integer $object_id    Identifiant de l'objet
   * @param string  $object_class Classe de l'objet
   *
   * @return integer Id du dossier médical
   */
  static function dossierMedicalId($object_id, $object_class) {
    $dossier = new CDossierMedical();
    $dossier->object_id    = $object_id;
    $dossier->object_class = $object_class;
    $dossier->loadMatchingObject();
    if (!$dossier->_id) {
      $dossier->store();
    }
    
    return $dossier->_id;
  }

  /**
   * @see parent::store()
   */
  function store() {
    $this->completeField("codes_cim");
    $this->_codes_cim = $this->codes_cim ? explode("|", $this->codes_cim) : array();

    if ($this->_added_code_cim) {
      $da = CCodeCIM10::get($this->_added_code_cim);
      if (!$da->exist) {
        CAppUI::setMsg("Le code CIM saisi n'est pas valide", UI_MSG_WARNING);
        return null;
      }
      $this->_codes_cim[] = $this->_added_code_cim;
    }

    if ($this->_deleted_code_cim) {
      CMbArray::removeValue($this->_deleted_code_cim, $this->_codes_cim);
    }

    $this->codes_cim = implode("|", array_unique($this->_codes_cim));
    
    $this->completeField("object_id", "object_class");
    if ($this->object_class == "CPatient" && $this->fieldModified("codes_cim")) {
      DSHM::remKeys("alertes-*-CPatient-".$this->object_id);
    }
    
    return parent::store();
  }

  /**
   * @see parent::fillTemplate()
   */
  function fillTemplate(&$template, $champ = "Patient") {
    // Antécédents
    $this->loadRefsAntecedents();
    $atcd = new CAntecedent();

    // Construction des listes de valeurs
    $lists_par_type     = array();
    $lists_par_appareil = array();
    foreach ($this->_all_antecedents as $_antecedent) {
      $type     = $_antecedent->type     ? $_antecedent->getFormattedValue("type"    ).": " : "";
      $appareil = $_antecedent->appareil ? $_antecedent->getFormattedValue("appareil").": " : "";
      $date = $_antecedent->date ? "[".$_antecedent->getFormattedValue("date")."] " : "";
      $lists_par_type    [$_antecedent->type    ][] = $appareil . $date . $_antecedent->rques;
      $lists_par_appareil[$_antecedent->appareil][] = $type     . $date . $_antecedent->rques;
    }

    // Séparateur pour les groupes de valeurs
    $default = CAppUI::pref("listDefault");
    $separator = CAppUI::pref("listInlineSeparator");
    $separators = array(
      "ulli"   => "",
      "br"     => "<br />",
      "inline" => " $separator ",
    );
    $separator = $separators[$default];

    // Création des listes par type
    $parts = array();
    $types = $atcd->_specs["type"]->_list;
    $types[] = "";

    foreach ($types as $type) {
      $sType =  CAppUI::tr("CAntecedent.type.$type");
      $list = @$lists_par_type[$type];
      if ($type) {
        $template->addListProperty("$champ - Antécédents - $sType", $list);
      }
      else {
        $template->addListProperty("$champ - Antécédents - Autres (type)", $list);
      }
      if ($list) {
        $parts[] = "<strong>$sType</strong>: " . $template->makeList($list);
      }
    }

    $template->addProperty("$champ - Antécédents - tous", implode($separator, $parts), null, false);
        
    // Création des listes par appareil
    $parts = array();
    $appareils = $atcd->_specs["appareil"]->_list;
    $appareils[] = "";
    foreach ($appareils as $appareil) {
      $sAppareil =  CAppUI::tr("CAntecedent.appareil.$appareil");
      $list = @$lists_par_appareil[$appareil];
      if ($appareil) {
        $template->addListProperty("$champ - Antécédents - $sAppareil", $list);
      }
      else {
        $template->addListProperty("$champ - Antécédents - Autres (appareil)", $list);
      }
      if ($list) {
        $parts[] = "<strong>$sAppareil</strong>: " . $template->makeList($list);
      }
    }
    $template->addProperty("$champ - Antécédents - tous par appareil", implode($separator, $parts), null, false);

    // Traitements
    $this->loadRefsTraitements();
    if (is_array($this->_ref_traitements)) {
      $list = array();
      /** @var $_traitement CTraitement */
      foreach ($this->_ref_traitements as $_traitement) {
        if ($_traitement->fin && $_traitement->fin <= CMbDT::date()) {
          continue;
        }
        $debut     = $_traitement->debut ? " depuis "   . $_traitement->getFormattedValue("debut") : "";
        $fin       = $_traitement->fin ? " jusqu'au " . $_traitement->getFormattedValue("fin"    ) : "";
        $colon  = $debut || $fin ? ": " : "";
        $list[] = $debut . $fin . $colon . $_traitement->traitement;
      }

      // Ajout des traitements notés a l'aide de la BCB
      $prescription = $this->loadRefPrescription();
      if ($prescription && $prescription->_id) {
        $prescription->loadRefsLinesMed();
        foreach ($prescription->_ref_prescription_lines as $_line) {
          if ($_line->fin && $_line->fin <= CMbDT::date()) {
            continue;
          }
          $view = $_line->_ucd_view;
          $prises = $_line->loadRefsPrises();
          $posologie = implode(" - ", CMbArray::pluck($prises, "_view"));
          $posologie = $posologie ? " ($posologie)" : "";
          $duree = "";
          if ($_line->debut && (!$_line->fin || $_line->fin >= CMbDT::date())) {
            if ($_line->fin) {
              $duree = " (Du " . $_line->getFormattedValue("debut"). "au " . $_line->getFormattedValue("fin").")";
            }
            else {
              $duree = " (Depuis le " . $_line->getFormattedValue("debut").")";
            }
          }
          elseif ($_line->fin && $_line->fin >= CMbDT::date()) {
            $duree = " (Jusqu'au " . $_line->getFormattedValue("fin").")";
          }

          $list[] = $view . $posologie. $duree . ($_line->commentaire ? "\n$_line->commentaire" : "");
        }
      }
      
      $template->addListProperty("$champ - Traitements", $list);
    }
    $template->addProperty("$champ - Traitements - Absence de traitement", $this->absence_traitement ? $this->getLocale("absence_traitement") : "");
    
    // Etat dentaire
    $etats = array();
    foreach ($this->loadRefsEtatsDents() as $etat) {
      if ($etat->etat) {
        switch ($etat->dent) {
          case 10: 
          case 50:
            $position = 'Central haut';
            break;
          case 30: 
          case 70:
            $position = 'Central bas';
            break;
          default: $position = $etat->dent;
        }
        if (!isset ($etats[$etat->etat])) {
          $etats[$etat->etat] = array();
        }
        $etats[$etat->etat][] = $position;
      }
    }
    
    // Production des listes par état
    $list = array();
    foreach ($etats as $etat => $positions) {
      sort($positions);
      $positions = implode(', ', $positions);
      $etat = CAppUI::tr("CEtatDent.etat.$etat");
      $list[] = "$etat: $positions";
    }

    $template->addListProperty("$champ - Etat dentaire", $list);
    
    // Codes CIM10
    $list = array();
    if ($this->_ext_codes_cim) {
      foreach ($this->_ext_codes_cim as $_code) {
        $list[] = "$_code->code: $_code->libelle";
      }
    }
    
    // Facteurs de risque
    switch ($champ) {
      case "Sejour":
        $template->addProperty(
          "Anesthésie - Maladie thrombo embolique - Chirurgie",
          $this->getFormattedValue("risque_thrombo_chirurgie")
        );
        $template->addProperty(
          "Anesthésie - MCJ - Chirurgie",
          $this->getFormattedValue("risque_MCJ_chirurgie")
        );
        $template->addProperty(
          "Anesthésie - Risque Anesthesique - Antibioprophylaxie",
          $this->getFormattedValue("risque_antibioprophylaxie")
        );
        $template->addProperty(
          "Anesthésie - Risque Anesthesique - Thrombo prophylaxie",
          $this->getFormattedValue("risque_prophylaxie")
        );
        break;
      case "Patient":
        $template->addProperty(
          "Anesthésie - Maladie thrombo embolique - Patient",
          $this->getFormattedValue("risque_thrombo_patient")
        );
        $template->addProperty(
          "Anesthésie - MCJ - Patient",
          $this->getFormattedValue("risque_MCJ_patient")
        );
        $template->addProperty(
          "Anesthésie - Facteurs de risque",
          $this->getFormattedValue("facteurs_risque")
        );
    }
    
    $template->addListProperty("$champ - Diagnostics", $list);
  }

  /**
   * Supprime du dossier médical les antécedents présents dans le dossier du séjour et dans le dossier du patient
   *
   * @param CDossierMedical &$dossier_sejour  Le dossier medical du sejours
   * @param CDossierMedical &$dossier_patient Le dossier medical du patient
   *
   * @return void
   */
  public static function cleanAntecedentsSignificatifs(&$dossier_sejour, &$dossier_patient) {
    $del_ante = 0;
    foreach ($dossier_sejour->_ref_antecedents_by_type as $_cat_name => $_cat_ante) {
      if ($_cat_name != 'alle') {
        foreach ($_cat_ante as $_key => $_ante) {
          foreach ($dossier_patient->_ref_antecedents_by_type[$_cat_name] as $_pat_key => $_pat_ante) {
            if (
                $_ante->type == $_pat_ante->type && $_ante->appareil == $_pat_ante->appareil && $_ante->date == $_pat_ante->date &&
                $_ante->rques == $_pat_ante->rques && $_ante->annule == $_pat_ante->annule
            ) {
              $del_ante++;
              unset($dossier_patient->_ref_antecedents_by_type[$_cat_name][$_pat_key]);
            }
          }
        }
      }
    }
    $dossier_patient->_count_antecedents = $dossier_patient->_count_antecedents - $del_ante;
  }
}
