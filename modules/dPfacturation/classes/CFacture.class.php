<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Facture générique
 */
class CFacture extends CMbObject {
  
  // DB Fields
  public $patient_id; 
  public $praticien_id;
  public $remise;
  public $ouverture;
  public $cloture;
  public $du_patient;
  public $du_tiers;
  public $type_facture;
  public $patient_date_reglement;
  public $tiers_date_reglement;
  public $npq;
  public $cession_creance;
  public $assurance_maladie;
  public $assurance_accident;
  public $rques_assurance_maladie;
  public $rques_assurance_accident;
  public $send_assur_base;
  public $send_assur_compl;
  public $facture;
  public $ref_accident;
  public $statut_pro;
  public $num_reference;
  public $envoi_xml;
  public $annule;
  
  // Form fields
  public $_consult_id;
  public $_total;
  public $_duplicate;
  
  public $_coeff;
  public $_montant_sans_remise;
  public $_montant_avec_remise;
  public $_secteur1 = 0.0;
  public $_secteur2 = 0.0;
  //Champ à supprimer
  public $_montant_total;
  
  public $_total_tarmed;
  public $_total_caisse;
  public $_autre_tarmed;
  
  public $_du_restant_patient;
  public $_du_restant_tiers;
  public $_reglements_total_patient;
  public $_reglements_total_tiers;
  public $_montant_factures        = array();
  public $_num_bvr                 = array();
  public $_montant_factures_caisse = array();
  public $_is_relancable;
  public $_montant_retrocession;
      
  // Object References
  /** @var CCorrespondantPatient */
  public $_ref_assurance_accident;
  /** @var CCorrespondantPatient */
  public $_ref_assurance_maladie;
  /** @var CMediusers */
  public $_ref_chir;
  /** @var CConsultation */
  public $_ref_last_consult;
  /** @var CConsultation */
  public $_ref_first_consult;
  /** @var CPatient */
  public $_ref_patient;
  /** @var CMediusers */
  public $_ref_praticien;
  /** @var CSejour */
  public $_ref_first_sejour;
  /** @var CSejour */
  public $_ref_last_sejour;
  /** @var CRelance */
  public $_ref_last_relance;

  // Object Collections
  /** @var CConsultation[] */
  public $_ref_consults;
  /** @var CFactureItem */
  public $_ref_items;
  /** @var CReglement[] */
  public $_ref_reglements;
  /** @var CReglement[] */
  public $_ref_reglements_patient;
  /** @var CReglement[] */
  public $_ref_reglements_tiers;
  /** @var CSejour[] */
  public $_ref_sejours;
  /** @var CRelance[] */
  public $_ref_relances;
  /** @var CActeTarmed[] */
  public $_ref_actes_tarmed = array();
  /** @var CActeCaisse[] */
  public $_ref_actes_caisse = array();
  /** @var CActeNGAP[] */
  public $_ref_actes_ngap   = array();
  /** @var CActeCCAM[] */
  public $_ref_actes_ccam   = array();
  
  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["items"] = "CFactureItem object_id";
    return $backProps;
  }
  
  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["patient_id"]    = "ref class|CPatient purgeable seekable notNull show|1";
    $props["praticien_id"]  = "ref class|CMediusers";
    $props["remise"]        = "currency default|0 decimals|2";
    $props["ouverture"]     = "date notNull";
    $props["cloture"]       = "date";
    $props["du_patient"]    = "currency notNull default|0 decimals|2";
    $props["du_tiers"]      = "currency notNull default|0 decimals|2";
    
    $props["type_facture"]              = "enum notNull list|maladie|accident default|maladie";
    $props["patient_date_reglement"]    = "date";
    $props["tiers_date_reglement"]      = "date";
    $props["npq"]                       = "bool default|0";
    $props["cession_creance"]           = "bool default|0";
    $props["assurance_maladie"]         = "ref class|CCorrespondantPatient";
    $props["assurance_accident"]        = "ref class|CCorrespondantPatient";
    $props["rques_assurance_maladie"]   = "text helped";
    $props["rques_assurance_accident"]  = "text helped";
    $props["send_assur_base"]           = "bool default|0";
    $props["send_assur_compl"]          = "bool default|0";
    $props["facture"]                   = "enum notNull list|-1|0|1 default|0";
    $props["ref_accident"]              = "text";
    $props["statut_pro"]                = "enum list|chomeur|etudiant|non_travailleur|independant|invalide|militaire|retraite|salarie_fr|salarie_sw|sans_emploi";
    $props["num_reference"]             = "str minLength|16 maxLength|27";
    $props["envoi_xml"]                 = "bool default|1";
    $props["annule"]                    = "bool default|0";

    $props["_du_restant_patient"]       = "currency";
    $props["_du_restant_tiers"]         = "currency";
    $props["_reglements_total_patient"] = "currency";
    $props["_reglements_total_tiers"]   = "currency";
    $props["_montant_sans_remise"]      = "currency";
    $props["_montant_avec_remise"]      = "currency";
    $props["_secteur1"]                 = "currency";
    $props["_secteur2"]                 = "currency";
    $props["_montant_total"]            = "currency";
    $specs["_total"]                    = "currency";
    $specs["_montant_retrocession"]     = "currency";
    return $props;
  }

  /**
   * Duplication de la facture
   *
   * @return void
   **/
  function duplicate() {
    $this->loadRefsReglements();
    $this->loadRefsRelances();
    if (!$this->_id || count($this->_ref_reglements) || count($this->_ref_relances)) {
      return null;
    }

    $new = new $this->_class;
    $new->cloneFrom($this);

    if ($msg = $new->store()) {
      return $msg;
    }

    $this->loadRefsItems();
    foreach ($this->_ref_items as $item) {
      $new_item = new CFactureItem();
      $new_item->cloneFrom($item, $new->_id);
      if ($msg = $new_item->store()) {
        return $msg;
      }
    }

    $liaison = new CFactureLiaison();
    $liaison->facture_id = $this->_id;
    $liaison->facture_class = $this->_class;
    $liaison->loadMatchingObject();

    $new_liaison = new CFactureLiaison();
    $new_liaison->cloneFrom($liaison, $new->_id);
    if ($msg = $new_liaison->store()) {
      return $msg;
    }

    return null;
  }

  /**
   * Redéfinition du store
   * 
   * @return void|string
  **/
  function store() {
    if ($this->_id && $this->_duplicate) {
      $this->_duplicate = null;
      $this->duplicate();
      $this->annule = 1;
    }

    if (!$this->cloture && $this->fieldModified("cloture") && count($this->_ref_reglements)) {
      return "Vous ne pouvez pas décloturer une facture ayant des règlements";
    }
    
    if (!$this->cloture && $this->fieldModified("cloture") && count($this->_ref_relances)) {
      return "Vous ne pouvez pas décloturer une facture ayant des relances";
    }
  
    $create_lignes = false;
    if (!$this->_id && CAppUI::conf("dPfacturation ".$this->_class." use_auto_cloture")) {
      $this->cloture    = CMbDT::date();
      $create_lignes = true;
    }
    
    //Si on cloture la facture création des lignes de la facture 
    //Si on décloture on les supprime
    if ($this->cloture && $this->fieldModified("cloture") && !$this->completeField("cloture")) {
      $create_lignes = true;
    }
    elseif (!$this->cloture && $this->fieldModified("cloture")) {
      //Suppression des tous les items de la facture
      $this->loadRefsItems();
      foreach ($this->_ref_items as $item) {
        $item->delete();
      }
    }
    
    // Etat des règlement à propager sur les consultations
    if ($this->fieldModified("patient_date_reglement") || $this->fieldModified("tiers_date_reglement")) {
      $this->loadRefsConsultation();
      foreach ($this->_ref_consults as $_consultation) {
        $_consultation->patient_date_reglement = $this->patient_date_reglement;
        $_consultation->tiers_date_reglement   = $this->tiers_date_reglement;
        if ($msg = $_consultation->store()) {
          return $msg;
        }
      }

      if ($this->isRelancable() && $this->_ref_last_relance->_id) {
        $this->_ref_last_relance->etat = $this->patient_date_reglement ? "regle" : "emise";
        $this->_ref_last_relance->store();
      }
    }
    
    //Lors de la validation de la cotation d'une consultation
    if ($this->_consult_id) {
      $consult = new CConsultation();
      $consult->load($this->_consult_id);
      $consult->loadRefPlageConsult();
        
      // Si la facture existe déjà on la met à jour
      $where = array();
      $ljoin = array();
      if (CAppUI::conf("ref_pays") == 2) {
        $where["patient_id"]    = "= '$consult->patient_id'";
        $where["praticien_id"]  = "= '".$consult->_ref_plageconsult->chir_id."'";
        $where["cloture"]       = "IS NULL";
      }
      else {
        $ljoin["facture_liaison"] =  "facture_liaison.facture_id = facture_cabinet.facture_id";
        $where["facture_liaison.object_id"]     = " = '$this->_consult_id'";
        $where["facture_liaison.object_class"]  = " = 'CConsultation'";
        $where["facture_liaison.facture_class"] = " = 'CFactureCabinet'";
      }
      
      //Si la facture existe déjà
      if ($this->loadObject($where, null, null, $ljoin)) {
        //Dans le cas Suisse
        if (CAppUI::conf("ref_pays") == 2 && CModule::getActive("dPfacturation")) {
          $ligne = new CFactureLiaison();
          $ligne->facture_id    = $this->_id;
          $ligne->facture_class = $this->_class;
          $ligne->object_id     = $this->_consult_id;
          $ligne->object_class  = 'CConsultation';
          if (!$ligne->loadMatchingObject()) {
            $ligne->store();
          }
        }
      }
      else {
        // Sinon on la crée
        $this->ouverture    = CMbDT::date();
        $this->patient_id   = $consult->patient_id;
        $this->praticien_id = $consult->_ref_plageconsult->chir_id;
        $this->type_facture = $consult->pec_at == 'arret' ? "accident" : "maladie";
        if (CAppUI::conf("dPfacturation CFactureCabinet use_auto_cloture")) {
          $this->cloture    = CMbDT::date();
          $create_lignes = true;
        }
      }
    }
    
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($create_lignes) {
      $this->creationLignesFacture();
    }
  }
  
  /**
   * Redéfinition du delete
   * 
   * @return void|string
  **/
  function delete() {
    if (count($this->_ref_reglements)) {
      return "Vous ne pouvez pas supprimer une facture ayant des règlements";
    }
  
    if (count($this->_ref_relances)) {
      return "Vous ne pouvez pas supprimer une facture ayant des relances";
    }
    
    if (CModule::getActive("dPfacturation")) {
      $where = array();
      $where["facture_id"]    = " = '$this->_id'";
      $where["facture_class"] = " = '$this->_class'";
      $where[] = "object_class = 'Sejour' OR object_class = 'CConsultation'";
      
      $liaison = new CFactureLiaison();
      $liaisons = $liaison->loadList($where);
      foreach ($liaisons as $lien) {
        if ($msg = $lien->delete()) {
          return $msg;
        }
      }
    }
    
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }
  }
  
  /**
   * Suppression d'une consult dans la facture
   * 
   * @return void
  **/
  function cancelConsult() {
    if ($this->_consult_id) {
      $this->loadRefPatient();
      $this->loadRefPraticien();
      $this->loadRefsObjects();
      if (count($this->_ref_consults) == 1 && count($this->_ref_sejours) == 0 && $this->_ref_last_consult->_id == $this->_consult_id) {
        $this->delete();
      }
      elseif (CModule::getActive("dPfacturation")) {
        $where = array();
        $where["facture_id"]    = " = '$this->_id'";
        $where["facture_class"] = " = '$this->_class'";
        $where["object_class"] = " = 'CConsultation'";
        $where["object_id"] = " = '$this->_consult_id'";
        
        $liaison = new CFactureLiaison();
        $liaison->loadObject($where);
        $liaison->delete();
      }
    }
  }
  
  /**
   * Mise à jour des montant secteur 1, 2 et totaux, utilisés pour la compta
   * 
   * @return void
  **/
  function updateMontants(){
    $this->_secteur1  = 0;
    $this->_secteur2  = 0;
    if (count($this->_ref_sejours) != 0 || count($this->_ref_consults) != 0) {
      if (!count($this->_ref_items)) {
        $this->du_patient = 0;
        $this->du_tiers   = 0;
        if (count($this->_ref_sejours)) {
          foreach ($this->_ref_sejours as $sejour) {
            foreach ($sejour->_ref_operations as $op) {
              foreach ($op->_ref_actes as $acte) {
                $this->_secteur1      += $acte->montant_base;
                $this->_secteur2      += $acte->montant_depassement;
              }
            }
            foreach ($sejour->_ref_actes as $acte) {
              $this->_secteur1      += $acte->montant_base;
              $this->_secteur2      += $acte->montant_depassement;
            }
            $this->du_patient += $this->_secteur1;
            $this->du_tiers   += $this->_secteur2;
          }
        }
        if (count($this->_ref_consults)) {
          foreach ($this->_ref_consults as $_consult) {
            $this->_secteur1 += $_consult->secteur1;
            $this->_secteur2 += $_consult->secteur2;
            $this->du_patient += $_consult->du_patient;
            $this->du_tiers   += $_consult->du_tiers;
          }
        }
        $this->_secteur1 *= $this->_coeff;
        $this->_secteur2 *= $this->_coeff;
      }
      else {
        foreach ($this->_ref_items as $item) {
          $this->_secteur1  += $item->_montant_total_base;
          $this->_secteur2  += $item->_montant_total_depassement;
        }
        
        if (!CAppUI::conf("dPccam CCodeCCAM use_cotation_ccam")) {
          $this->du_patient = $this->_secteur1;
          $this->du_tiers   = $this->_secteur2;
        }
      }
    }
  }
  
  /**
   * Eclatement des montants de la facture utilisé uniquement en Suisse
   * 
   * @return void
  **/
  function eclatementTarmed() {
    if ($this->npq) {
      $this->remise = sprintf("%.2f", (10*(($this->du_patient+$this->du_tiers)*$this->_coeff))/100);
    }
    $this->_montant_factures   = array();
    $this->_montant_factures[] = $this->du_patient + $this->du_tiers;
    $this->loadNumerosBVR();
  }
          
  /**
   * Chargement du patient concerné par la facture
   * 
   * @param bool $cache cache
   * 
   * @return CPatient
  **/
  function loadRefPatient($cache = 1) {
    if (!$this->_ref_patient) {
      $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
      $this->_ref_patient->loadRefsCorrespondantsPatient();
    }
    return $this->_ref_patient;
  }
   
  /**
   * Chargement du praticien de la facture
   * 
   * @return CUser
  **/
  function loadRefPraticien() {
    if (!$this->_ref_praticien) {
      $this->_ref_praticien = $this->loadFwdRef("praticien_id", true);
    }
    return $this->_ref_praticien;
  }
  
  /**
   * Chargement des règlements de la facture
   * 
   * @return $this->_ref_reglements
  **/
  function loadRefsReglements() {
    $this->_montant_sans_remise = 0;
    $this->_montant_avec_remise = 0;
    
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
      foreach ($this->_montant_factures as $_montant) {
        $this->_montant_sans_remise += $_montant;
      }
      $this->_montant_avec_remise = $this->_montant_sans_remise - $this->remise;
    }
    
    if (!$this->_montant_sans_remise) {
      $this->_montant_sans_remise = $this->du_patient  + $this->du_tiers;
      $this->_montant_avec_remise = $this->_montant_sans_remise - $this->remise;
    }
    
    $this->_du_restant_patient = $this->du_patient;
    $this->_du_restant_tiers   = $this->du_tiers  ;
    
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
      $this->_du_restant_patient = $this->_montant_avec_remise;
    }
    
    // Calcul des dus
    $this->_reglements_total_patient = 0.00;
    $this->_reglements_total_tiers   = 0.00;
    $this->_ref_reglements_patient = array();
    $this->_ref_reglements_tiers   = array();
    foreach ($this->_ref_reglements as $_reglement) {
      $_reglement->loadRefBanque();
      
      if ($_reglement->emetteur == "patient") {
        $this->_ref_reglements_patient[] = $_reglement;
        $this->_du_restant_patient       -= $_reglement->montant;
        $this->_reglements_total_patient += $_reglement->montant;
      }
      
      if ($_reglement->emetteur == "tiers") {
        $this->_ref_reglements_tiers[] = $_reglement;
        $this->_du_restant_tiers       -= $_reglement->montant;
        $this->_reglements_total_tiers += $_reglement->montant;
      }
    }
    $this->_du_restant_patient = round($this->_du_restant_patient, 2);
    
    return $this->_ref_reglements;
  }
  
  /**
   * loadRefs
   * 
   * @return void
  **/
  function loadRefs(){
    //@todo fonction à supprimer
    $this->loadRefCoeffFacture();
    $this->loadRefPatient();
    $this->loadRefPraticien();
    $this->loadRefAssurance();
    $this->loadRefsObjects();
    $this->loadRefsItems();
    $this->loadRefsReglements();
  }
  
  /**
   * Dans la cas de la cotation d'acte Tarmed un facture comporte un coefficient (entre 0 et 1)
   * 
   * @return void
  **/
  function loadRefCoeffFacture() {
    $this->_coeff = 1;
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
      $statuts_maladie = array("sans_emploi", "etudiant", "non_travailleur", "independant");
      if ($this->statut_pro && in_array($this->statut_pro, $statuts_maladie, 1) && $this->type_facture == "accident") {
        $this->_coeff = CAppUI::conf("tarmed CCodeTarmed pt_maladie");
      }
      elseif ($this->statut_pro && $this->statut_pro == "invalide") {
        $this->_coeff = CAppUI::conf("tarmed CCodeTarmed pt_invalidite");
      }
      else {
        $this->_coeff = $this->type_facture == "accident" ?
          CAppUI::conf("tarmed CCodeTarmed pt_accident") :
          CAppUI::conf("tarmed CCodeTarmed pt_maladie");
      }
    }
  }
  
  /**
   * Chargement de l'assurance de la facture si elle a été choisie
   * 
   * @return object
  **/
  function loadRefAssurance() {
    $this->_ref_assurance_maladie = $this->loadFwdRef("assurance_maladie", true);
    $this->_ref_assurance_accident = $this->loadFwdRef("assurance_accident", true);
    return $this->_ref_assurance_maladie;
  }
  
  /**
   * Chargement des séjours et des consultations de la facture
   * 
   * @return void
  **/
  function loadRefsObjects(){
    $this->loadRefsConsultation();
    $this->loadRefsSejour();
    $this->loadRefCoeffFacture();
    
    // Eclatement des factures
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ) {
      $this->eclatementTarmed();
    }
    
    $this->updateMontants();
  }
  /**
   * Chargement de toutes les consultations de la facture
   * 
   * @return object
  **/
  function loadRefsConsultation() {
    if (count($this->_ref_consults)) {
      return $this->_ref_consults;
    }
    
    $consult = new CConsultation();
    if ($this->_id) {
      if (CModule::getActive("dPfacturation")) {
        $ljoin = array();
        $ljoin["facture_liaison"] = "facture_liaison.object_id = consultation.consultation_id";
        $where = array();
        $where["facture_liaison.facture_id"]    = " = '$this->_id'";
        $where["facture_liaison.facture_class"] = " = '$this->_class'";
        $where["facture_liaison.object_class"]  = " = 'CConsultation'";
        $this->_ref_consults = $consult->loadList($where, null, null, null, $ljoin);
      }
      if (!count($this->_ref_consults) && $this->_class == "CFactureCabinet") {
        $this->_ref_consults = $this->loadBackRefs("consultations", "consultation_id");
      }
    }
    elseif ($this->_consult_id) {
      $consult_new = new CConsultation();
      $consult_new->consultation_id = $this->_consult_id;
      $consult_new->loadMatchingObject();
      if ($consult_new->facture_id) {
        $consult->facture_id = $consult_new->facture_id;
      }
      else {
        $consult->consultation_id = $this->_consult_id;
      }
      $this->_ref_consults = $consult->loadMatchingList();
    }
    
    if (count($this->_ref_consults) > 0) {
      // Chargement des actes de consultations
      foreach ($this->_ref_consults as $_consult) {
        $_consult->loadRefsActes();
        $_consult->loadExtCodesCCAM();
        $this->rangeActes($_consult);
      }
      $this->_ref_last_consult = end($this->_ref_consults);
      $this->_ref_first_consult = reset($this->_ref_consults);
    }
    else {
      $this->_ref_last_consult = new CConsultation();
      $this->_ref_first_consult  = new CConsultation();
    }
    return $this->_ref_consults;
  }
  
  /**
   * Chargement de tous les séjours de la facture
   * 
   * @return object
  **/
  function loadRefsSejour() {
    if (count($this->_ref_sejours)) {
      return $this->_ref_sejours;
    }
    if (CModule::getActive("dPfacturation")) {
      $ljoin = array();
      $ljoin["facture_liaison"] = "facture_liaison.object_id = sejour.sejour_id";
      $where = array();
      $where["facture_liaison.facture_id"]    = " = '$this->_id'";
      $where["facture_liaison.facture_class"] = " = '$this->_class'";
      $where["facture_liaison.object_class"]  = " = 'CSejour'";
      
      $sejour = new CSejour();
      $this->_ref_sejours = $sejour->loadList($where, "sejour_id", null, null, $ljoin);
      // Chargement des actes de séjour
      foreach ($this->_ref_sejours as $sejour) {
        $sejour->loadRefsOperations();
        foreach ($sejour->_ref_operations as $op) {
          $op->loadRefsActes();
          $this->rangeActes($op);
        }
        $sejour->loadRefsActes();
        $this->rangeActes($sejour);
      }
    }
    if (count($this->_ref_sejours) > 0) {
      $this->_ref_last_sejour  = end($this->_ref_sejours);
      $this->_ref_first_sejour = reset($this->_ref_sejours);
      $this->_ref_last_sejour->loadRefLastOperation();
      $this->_ref_last_sejour->_ref_last_operation->loadRefAnesth();
    }
    else {
      $this->_ref_last_sejour = new CSejour();
      $this->_ref_first_sejour  = new CSejour();
    }
    return $this->_ref_sejours;
  }
  
  /**
   * Chargement des items de la facture
   * 
   * @return object
  **/
  function loadRefsItems(){
    $item =  new CFactureItem();
    $item->object_id   = $this->_id;
    $item->object_class = $this->_class;
    $this->_ref_items = $item->loadMatchingList("code ASC");
    if (count($this->_ref_items)) {
      $this->_ref_actes_tarmed = array();
      $this->_ref_actes_caisse = array();
      $this->_ref_actes_ngap = array();
      $this->_ref_actes_ccam = array();
      $this->rangeActes($this, false);
    }
    return $this->_ref_items;
  }
  
  /**
   * Ligne de report pour calculer un numéro de BVR pour la facture 
   * 
   * @param string $report l'élément à reporter
   * 
   * @return string
  **/
  function ligneReport($report){
    $etalon = ('09468271350946827135');
    $lignereport = substr($etalon, $report, 10);
    return $lignereport;
  }
  
  /**
   * Création du numéro de contrôle du BVR à l'aide d'un modulo 10
   * 
   * @param string $noatraiter le début du numéro de BVR pour obtenir le numéro de controle
   * 
   * @return string
  **/
  function getNoControle($noatraiter){
    if (!$noatraiter) {
      $noatraiter = $this->du_patient + $this->du_tiers;
    }
    $noatraiter = str_replace(' ', '', $noatraiter);
    $noatraiter = str_replace('-', '', $noatraiter);
    $report = 0;
    $cpt = strlen($noatraiter);
    for ($i = 0; $i < $cpt; $i++) {
      $report = substr($this->lignereport($report), substr($noatraiter, $i, 1), 1);
    }
    $report =  (10 - $report) % 10;
    return $report;
  }
  
  /**
   * Chargement des différents numéros de BVR de la facture 
   * 
   * @return void|string
  **/
  function loadNumerosBVR(){
    $use_tarmed = CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed");
    if ($use_tarmed && !count($this->_montant_factures_caisse)) {
      $this->_total_tarmed = 0;
      $this->_total_caisse = 0;
      $this->_autre_tarmed = 0;
      $this->loadTotaux();
      
      $montant_prem = round($this->_total_tarmed * $this->_coeff + $this->_autre_tarmed, 1);
      $this->_total_caisse = round($this->_total_caisse, 1);
      
      if ($montant_prem < 0) {
        $montant_prem = 0;
      }
      if ($this->_total_tarmed || $this->_autre_tarmed) {
         $this->_montant_factures_caisse[0] = sprintf("%.2f", $montant_prem - $this->remise);
      }
      if ($this->_total_caisse > 0) {
        $this->_montant_factures_caisse[1] = $this->_total_caisse;
      }
      
      $this->_montant_sans_remise = round($montant_prem + $this->_total_caisse, 1);
      $this->_montant_avec_remise = round($this->_montant_sans_remise - $this->remise, 1);
      if (count($this->_montant_factures) == 1) {
        $this->_montant_factures = $this->_montant_factures_caisse;
      }
      else {
        $this->_montant_factures_caisse = $this->_montant_factures;
      }
    
      if (!$this->_ref_praticien) {
        $this->loadRefPraticien();
      }
      
      // Le numéro de référence doit comporter 16 ou 27 chiffres
      $num = $this->_id;
      $nbcolonnes = 27 - strlen($this->_ref_praticien->debut_bvr);
      $num = sprintf("%0".$nbcolonnes."s", $num);
      $num = $this->_ref_praticien->debut_bvr.$num;
      if ((!$this->num_reference || $num != $this->num_reference)) {
        $this->num_reference = $num;
        $this->store();
      }
      
      $genre = "01";
      $adherent2 = str_replace(' ', '', $this->_ref_praticien->adherent);
      $adherent2 = str_replace('-', '', $adherent2);
      foreach ($this->_montant_factures_caisse as $montant_facture) {
        $montant = sprintf('%010d', $montant_facture*100);
        $cle = $this->getNoControle($genre.$montant);
        $this->_num_bvr[$montant_facture] = $genre.$montant.$cle.">".$this->num_reference."+ ".$adherent2.">";
      }
    }
    return $this->_num_bvr;
  }

  /**
   * Chargement des différents numéros de BVR de la facture 
   * 
   * @return void
  **/
  function loadTotaux() {
    $this->loadRefsItems();
    if ($this->cloture && count($this->_ref_items)) {
      foreach ($this->_ref_actes_tarmed as $acte_tarmed) {
        $this->_total_tarmed += $acte_tarmed->montant_base * $acte_tarmed->quantite;
      }
      foreach ($this->_ref_actes_caisse as $acte_caisse) {
        $this->completeField("type_facture");
        $coeff = "coeff_".$this->type_facture;
        if ($acte_caisse->_class == "CActeCaisse") {
          $coeff = $acte_caisse->_ref_caisse_maladie->$coeff;
          $use   = $acte_caisse->_ref_caisse_maladie->use_tarmed_bill;
        }
        else {
          $coeff = $acte_caisse->coeff ;
          $use   = $acte_caisse->use_tarmed_bill;
        }

        $tarif_acte_caisse = ($acte_caisse->_montant_facture)* $coeff *$acte_caisse->quantite ;
        if ($use) {
          $this->_autre_tarmed += $tarif_acte_caisse;
        }
        else {
          $this->_total_caisse +=  $tarif_acte_caisse;
        }
      }
    }
    else {
      if (count($this->_ref_consults)) {
        foreach ($this->_ref_consults as $consult) {
          $this->loadTotauxObject($consult);
        }
      }
      if (count($this->_ref_sejours)) {
        foreach ($this->_ref_sejours as $sejour) {
          foreach ($sejour->_ref_operations as $op) {
            $this->loadTotauxObject($op);
          }
          $this->loadTotauxObject($sejour);
        }
      }
    }
  }
  
  /**
   * Calcul des totaux à partir d'un objet
   * 
   * @param string $object objet référence
   * 
   * @return void
  **/
  function loadTotauxObject($object) {
    foreach ($object->_ref_actes_tarmed as $acte_tarmed) {
      $this->_total_tarmed += $acte_tarmed->_montant_facture * $acte_tarmed->quantite;
    }
    foreach ($object->_ref_actes_caisse as $acte_caisse) {
      $coeff = "coeff_".$this->type_facture;
      $tarif_acte_caisse = ($acte_caisse->_montant_facture)*$acte_caisse->_ref_caisse_maladie->$coeff * $acte_caisse->quantite;
      if ($acte_caisse->_ref_caisse_maladie->use_tarmed_bill) {
        $this->_autre_tarmed += $tarif_acte_caisse ;
      }
      else {
        $this->_total_caisse +=  $tarif_acte_caisse;
      }
    }
  }
  /**
   * Fonction de création des lignes(items) de la facture lorsqu'elle est cloturée
   * 
   * @param string  $object objet référence
   * @param boolean $val    item
   * 
   * @return void
  **/
  function rangeActes($object, $val = true) {
    $objets = $val ? $object->_ref_actes : $object->_ref_items;
    $type = $val ? "_class" : "type";
    if (count($objets)) {
      foreach ($objets as $key => $acte) {
        switch ($acte->$type) {
          case "CActeTarmed" :
            $this->_ref_actes_tarmed[$key] = $acte;
            break;
          case "CActeCaisse" :
            $this->_ref_actes_caisse[$key] = $acte;
            break;
          case "CActeNGAP" :
            $this->_ref_actes_ngap[$key] = $acte;
            break;
          case "CActeCCAM" :
            if ($type == "_class") {
              $acte->loadRefCodeCCAM();
            }
            $this->_ref_actes_ccam[$key] = $acte;
            break;
        }
      }
    }
  }
  
  /**
   * Fonction de création des lignes(items) de la facture lorsqu'elle est cloturée
   * 
   * @return void
  **/
  function creationLignesFacture() {
    $this->loadRefCoeffFacture();
    $this->loadRefsConsultation();
    foreach ($this->_ref_consults as $consult) {
      foreach ($consult->_ref_actes as $acte) {
        $acte->creationItemsFacture($this, $consult->_date);
      }
    }
    $this->loadRefsSejour();
    foreach ($this->_ref_sejours as $sejour) {
      foreach ($sejour->_ref_operations as $op) {
        foreach ($op->_ref_actes as $acte) {
          $acte->creationItemsFacture($this, $op->date);
        }
      }
      foreach ($sejour->_ref_actes as $acte) {
        $acte->creationItemsFacture($this, $sejour->entree_prevue);
      }
    }
  }
  
  /**
   * Fonction permettant de savoir si la facture doit être relancée
   * 
   * @return boolean
  **/
  function isRelancable() {
    $this->_is_relancable = false;

    if (!CAppUI::conf("dPfacturation CRelance use_relances")) {
      return $this->_is_relancable;
    }

    $date = CMbDT::date();

    $nb_first_relance  = CAppUI::conf("dPfacturation CRelance nb_days_first_relance");
    $nb_second_relance = CAppUI::conf("dPfacturation CRelance nb_days_second_relance");

    $this->_ref_last_relance = !count($this->_ref_relances) ? new CRelance() : end($this->_ref_relances);

    if (($this->_du_restant_patient > 0 || $this->_du_restant_tiers > 0) && $this->cloture) {
      if (!count($this->_ref_relances) && CMbDT::daysRelative($this->cloture, $date) > $nb_first_relance) {
        $this->_is_relancable = true;
      }
      elseif (count($this->_ref_relances)) {
        if (CMbDT::daysRelative($this->_ref_last_relance->date, $date) > $nb_second_relance) {
          $this->_is_relancable = true;
        }
      }
    }

    return $this->_is_relancable;
  }
  
  /**
   * Calcul du montant de la retrocession pour la facture
   * 
   * @return boolean
  **/
  function updateMontantRetrocession() {
    $this->_montant_retrocession = 0;
    $this->loadRefPraticien();
    $this->loadRefsItems();
    $retrocessions = $this->_ref_praticien->loadRefsRetrocessions();
    foreach ($this->_ref_items as $item) {
      foreach ($retrocessions as $retro) {
        if ($retro->code_class == $item->type && $retro->code == $item->code) {
          $this->_montant_retrocession += $retro->updateMontant();
        }
      }
    }
    return $this->_montant_retrocession;
  }

  /**
   * Clonage des éléments de la facture
   *
   * @param object $the_facture la facture
   *
   * @return void
   */
  function cloneFrom($the_facture){
    $facture = new $the_facture->_class;
    $facture->load($the_facture->_id);
    $this->patient_id = $facture->patient_id;
    $this->praticien_id = $facture->praticien_id;
    $this->remise = $facture->remise;
    $this->ouverture = $facture->ouverture;
    $this->cloture = $facture->cloture;
    $this->du_patient = $facture->du_patient;
    $this->du_tiers = $facture->du_tiers;
    $this->type_facture = $facture->type_facture;
    $this->npq = $facture->npq;
    $this->cession_creance = $facture->cession_creance;
    $this->assurance_maladie = $facture->assurance_maladie;
    $this->assurance_accident = $facture->assurance_accident;
    $this->rques_assurance_maladie = $facture->rques_assurance_maladie;
    $this->rques_assurance_accident = $facture->rques_assurance_accident;
    $this->send_assur_base = $facture->send_assur_base;
    $this->send_assur_compl = $facture->send_assur_compl;
    $this->facture = $facture->facture;
    $this->ref_accident = $facture->ref_accident;
    $this->statut_pro = $facture->statut_pro;
    $this->num_reference = $facture->num_reference;
    $this->envoi_xml = $facture->envoi_xml;
  }
}
