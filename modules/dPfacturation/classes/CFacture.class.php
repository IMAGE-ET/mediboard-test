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
 *
 */
class CFacture extends CMbObject {
  
  // DB Fields
  var $patient_id   = null; 
  var $praticien_id = null; 
  var $remise       = null; 
  var $ouverture    = null; 
  var $cloture      = null;
  var $du_patient   = null;
  var $du_tiers     = null;
  var $type_facture           = null;
  var $patient_date_reglement = null;
  var $tiers_date_reglement   = null;
  var $npq                    = null;
  var $cession_creance        = null;
  var $assurance_maladie      = null;
  var $assurance_accident     = null;
  var $rques_assurance_maladie  = null;
  var $rques_assurance_accident = null;
  var $send_assur_base        = null;
  var $send_assur_compl       = null;
  var $facture                = null;
  var $ref_accident           = null;
  var $statut_pro             = null;
  var $num_reference          = null;
  var $envoi_xml              = null;
  
  // Form fields
  var $_consult_id  = null;
  var $_total       = null;
  
  var $_nb_factures         = null;
  var $_coeff               = null;
  var $_montant_sans_remise = null;
  var $_montant_avec_remise = null;
  var $_montant_secteur1    = null;
  var $_montant_secteur2    = null;
  var $_montant_total       = null;
  
  var $_total_tarmed        = null;
  var $_total_caisse        = null;
  var $_autre_tarmed        = null;
  
  var $_du_restant_patient        = null;
  var $_du_restant_tiers          = null;
  var $_reglements_total_patient  = null;
  var $_reglements_total_tiers    = null;
  var $_montant_factures          = array();
  var $_num_bvr                   = array();
  var $_montant_factures_caisse   = array();
  //champ de test rapide
  var $_ajoute   = null;
      
  // Object References
  var $_ref_assurance_accident  = null;
  var $_ref_assurance_maladie   = null;
  var $_ref_chir                = null;
  var $_ref_consults            = null;
  var $_ref_last_consult        = null;
  var $_ref_first_consult       = null;
  var $_ref_items               = null;
  var $_ref_patient             = null;
  var $_ref_praticien           = null;
  var $_ref_reglements          = null;
  var $_ref_reglements_patient  = null;
  var $_ref_reglements_tiers    = null;
  var $_ref_sejours             = null;
  
  var $_ref_actes_tarmed  = array();
  var $_ref_actes_caisse  = array();
  var $_ref_actes_ngap    = array();
  var $_ref_actes_ccam    = array();
  
  /**
   * getBackProps
   * 
   * @return $backProps
  **/
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["items"] = "CFactureItem object_id";
    return $backProps;
  }
   
  /**
   * getProps
   * 
   * @return $props
  **/
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
    $props["npq"]                       = "enum notNull list|0|1 default|0";
    $props["cession_creance"]           = "enum notNull list|0|1 default|0";
    $props["assurance_maladie"]         = "ref class|CCorrespondantPatient";
    $props["assurance_accident"]        = "ref class|CCorrespondantPatient";
    $props["rques_assurance_maladie"]   = "text helped";
    $props["rques_assurance_accident"]  = "text helped";
    $props["send_assur_base"]           = "bool default|0";
    $props["send_assur_compl"]          = "bool default|0";
    $props["facture"]                   = "enum notNull list|-1|0|1 default|0";
    $props["ref_accident"]              = "text";
    $props["statut_pro"]                = "enum list|chomeur|etudiant|non_travailleur|independant|salarie|sans_emploi";
    $props["num_reference"]             = "str minLength|16 maxLength|27";
    $props["envoi_xml"]                 = "bool default|1";
    
    $props["_du_restant_patient"]       = "currency";
    $props["_du_restant_tiers"]         = "currency";
    $props["_reglements_total_patient"] = "currency";
    $props["_reglements_total_tiers"]   = "currency";
    $props["_montant_sans_remise"]      = "currency";
    $props["_montant_avec_remise"]      = "currency";
    $props["_montant_secteur1"]         = "currency";
    $props["_montant_secteur2"]         = "currency";
    $props["_montant_total"]            = "currency";
    $specs["_total"]                    = "currency";
    return $props;
  }
  
  /**
   * updateFormFields
   * 
   * @return void
  **/
  function updateFormFields() {
    parent::updateFormFields();
  }
  
  /**
   * loadRefsFwd
   * 
   * @return void
  **/
  function loadRefsFwd(){
    $this->loadRefPatient();
    $this->loadRefPraticien();
    $this->loadRefAssurance();
    $this->loadRefsItems();
  } 
  
  /**
   * Redéfinition du store
   * 
   * @return void
  **/
  function store() {
    if (CAppUI::conf("dPfacturation $this->_class create_items_bill")) {
      if (!$this->cloture && $this->fieldModified("cloture")) {
        $this->deleteItems();
      }
    }
    
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
  }
  
  /**
   * Redéfinition du delete
   * 
   * @return void
  **/
  function delete() {
    if (CAppUI::conf("dPfacturation CFactureCabinet use_create_bill")) {
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
    // Standard store
    if ($msg = parent::delete()) {
      return $msg;
    }
  }
  
  /**
   * Suppression des tous les items de la facture
   * 
   * @return void
  **/
  function deleteItems() {
    $this->loadRefsItems();
    foreach ($this->_ref_items as $item) {
      $item->delete();
    }
  }
  /**
   * Mise à jour des montant secteur 1, 2 et totaux, utilisés pour la comtpa
   * 
   * @return void
  **/
  function updateMontants() {
    $this->_montant_secteur1 = 0.0;
    $this->_montant_secteur1 = 0.0;
    $this->_montant_total    = 0.0;
    $this->du_patient        = 0;
    
    if (count($this->_ref_sejours) == 0 && count($this->_ref_consults) == 0) {
      $this->delete();
    }
    else {
      if (count($this->_ref_sejours)) {
        foreach ($this->_ref_sejours as $sejour) {
          foreach ($sejour->_ref_operations as $op) {
            foreach ($op->_ref_actes_tarmed as $acte) {
              $this->_montant_secteur1 += $acte->montant_base;
              $this->du_patient        += $acte->montant_base;
              $this->_montant_secteur2 += $acte->montant_depassement;
              $this->_montant_total    += ($acte->montant_base + $acte->montant_depassement);
            }
            foreach ($op->_ref_actes_caisse as $acte) {
              $this->_montant_secteur1 += $acte->montant_base;
              $this->du_patient        += $acte->montant_base;
              $this->_montant_secteur2 += $acte->montant_depassement;
              $this->_montant_total    += ($acte->montant_base + $acte->montant_depassement);
            }
          }
        }
      }
      if (count($this->_ref_consults)) {
        foreach ($this->_ref_consults as $_consult) {
          $this->_montant_secteur1 += $_consult->secteur1;
          $this->_montant_secteur2 += $_consult->secteur2;
          $this->_montant_total    += $_consult->_somme;
          $this->du_patient        += $_consult->du_patient;
        }
      }
      $this->_montant_secteur1 *= $this->_coeff;
      $this->_montant_secteur2 *= $this->_coeff;
    }
  }
  
  /**
   * Mise à jour des montant secteur 1, 2 et totaux, utilisés pour la comtpa
   * 
   * @return void
  **/
  function updateMontantsFacture() {
//    $this->loadRefsObjects();
//    $this->updateMontants();
    $this->du_patient = $this->_montant_secteur1;
    $this->du_tiers   = $this->_montant_secteur2;
    if ($this->_id) {
      $this->store();
    }
  }
  
  /**
   * Eclatement des montants de la facture utilisé uniquement en Suisse 
   * 
   * @return void
  **/
  function eclatementTarmed() {
    if ($this->npq) {
      $this->remise = sprintf("%.2f",(10*(($this->du_patient+$this->du_tiers)*$this->_coeff))/100);
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
   * @return void
  **/
  function loadRefPraticien(){
    if (!$this->_ref_praticien) {
      $this->_ref_praticien = $this->loadFwdRef("praticien_id", true);
    }
    return $this->_ref_praticien;
  }
  
  /**
   * Chargement des règlements de la facture
   * 
   * @param bool $cache cache
   * 
   * @return $this->_ref_reglements
  **/
  function loadRefsReglements($cache = 1) {
    $this->_montant_sans_remise = 0;
    
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
   * loadRefsBack
   * 
   * @return void
  **/
  function loadRefsBack(){
    $this->loadRefsReglements();
  }
  
  /**
   * loadRefs
   * 
   * @return void
  **/
  function loadRefs(){
    $this->loadRefCoeffFacture();
    $this->loadRefsFwd();
    $this->loadRefsBack();
  }
  
  /**
   * Dans la cas de la cotation d'acte Tarmed un facture comporte un coefficient (entre 0 et 1)
   * 
   * @return void
  **/
  function loadRefCoeffFacture() {
    $this->_coeff = 1;
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")) {
      if ($this->statut_pro && ($this->statut_pro == "sans_emploi" || $this->statut_pro == "etudiant" || $this->statut_pro == "non_travailleur") && $this->type_facture == "accident") {
        $this->_coeff = CAppUI::conf("tarmed CCodeTarmed pt_maladie");
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
    $this->_nb_factures = 1;
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
    if (CAppUI::conf("dPfacturation CFactureCabinet use_create_bill")) {
      $ljoin = array();
      $ljoin["facture_liaison"] = "facture_liaison.object_id = consultation.consultation_id";
      $where = array();
      $where["facture_liaison.facture_id"]    = " = '$this->_id'";
      $where["facture_liaison.facture_class"] = " = '$this->_class'";
      $where["facture_liaison.object_class"]  = " = 'CConsultation'";
      
      $this->_ref_consults = $consult->loadList($where, null, null, null, $ljoin);
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
    elseif ($this->_id) {
      $consult->facture_id = $this->_id;
      $this->_ref_consults = $consult->loadMatchingList();
    }
    
    if (count($this->_ref_consults) > 0) {
      foreach ($this->_ref_consults as $_consult) {
        if ($_consult->valide == 0) {
          $liaison = new CFactureLiaison();
          $liaison->facture_class = $this->_class;
          $liaison->facture_id    = $this->_id;
          $liaison->object_class  = "CConsultation";
          $liasion->object_id     = $_consult->_id;
          $liaison->loadMatchingObject();
          unset($this->_ref_consults["$_consult->_id"]);
        }  
      }
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
      $sejour->loadRefsBack();
      foreach ($sejour->_ref_operations as $op) {
        $op->loadRefsActes();
        $this->rangeActes($op);
      }
      $sejour->loadRefsActes();
      $this->rangeActes($sejour);
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
  }

  /**
   * Création d'un item de facture avec un code ccam
   * 
   * @param string $acte_ccam acte référence
   * @param string $date      date à défaut
   * 
   * @return void
  **/
  function creationLigneCCAM($acte_ccam, $date){
    $ligne = new CFactureItem();
    $ligne->libelle       = $acte_ccam->_ref_code_ccam->libelleCourt;
    $ligne->code          = $acte_ccam->code;
    $ligne->type          = $acte_ccam->_class;
    $ligne->object_id    = $this->_id;
    $ligne->object_class = $this->_class;
    $ligne->date          = mbDate($acte_ccam->execution);
    $ligne->prix          = $acte_ccam->montant_base;
    $ligne->quantite      = 1;
    $ligne->coeff         = $this->_coeff;
    if ($msg = $ligne->store()) {
      return $msg;
    }
  }
  /**
   * Création d'un item de facture avec un code ngap
   * 
   * @param string $acte_ngap acte référence
   * @param string $date      date à défaut
   * 
   * @return void
  **/
  function creationLigneNGAP($acte_ngap, $date){
    $ligne = new CFactureItem();
    $ligne->libelle       = $acte_ngap->_libelle;
    $ligne->code          = $acte_ngap->code;
    $ligne->type          = $acte_ngap->_class;
    $ligne->object_id    = $this->_id;
    $ligne->object_class = $this->_class;
    $ligne->date          = $date;
    $ligne->prix          = $acte_ngap->montant_base;
    $ligne->quantite      = $acte_ngap->quantite;
    $ligne->coeff         = $acte_ngap->coefficient;
    if ($msg = $ligne->store()) {
      return $msg;
    }
  }
  
  /**
   * Création d'un item de facture avec un code tarmed
   * 
   * @param string $acte_tarmed acte référence
   * @param string $date        date à défaut
   * 
   * @return void
  **/
  function creationLigneTarmed($acte_tarmed, $date){
    $ligne = new CFactureItem();
    $ligne->libelle       = $acte_tarmed->_ref_tarmed->libelle;
    $ligne->code          = $acte_tarmed->code;
    $ligne->type          = $acte_tarmed->_class;
    $ligne->object_id    = $this->_id;
    $ligne->object_class = $this->_class;
    if ($acte_tarmed->date) {
      $ligne->date          = $acte_tarmed->date;
    }
    else {
      $ligne->date          = $date;
    }
    $ligne->prix          = $acte_tarmed->montant_base;
    $ligne->quantite      = $acte_tarmed->quantite;
    $ligne->pm            = $acte_tarmed->_ref_tarmed->tp_al;
    $ligne->pt            = $acte_tarmed->_ref_tarmed->tp_tl;
    $ligne->coeff_pm      = $acte_tarmed->_ref_tarmed->f_al;
    $ligne->coeff_pt      = $acte_tarmed->_ref_tarmed->f_tl;
    $ligne->coeff         = $this->_coeff;
    if ($msg = $ligne->store()) {
      return $msg;
    }
  }

  /**
   * Création d'un item de facture avec un code caisse
   * 
   * @param string $acte_caisse acte référence
   * @param string $date        date à défaut
   * 
   * @return void
  **/
  function creationLigneCaisse($acte_caisse, $date){
    $ligne = new CFactureItem();
    $ligne->libelle       = $acte_caisse->_ref_prestation_caisse->libelle;
    $ligne->code          = $acte_caisse->code;
    $ligne->type          = $acte_caisse->_class;
    $ligne->object_id    = $this->_id;
    $ligne->object_class = $this->_class;
    if ($acte_caisse->date) {
      $ligne->date          = $acte_caisse->date;
    }
    else {
      $ligne->date          = $date;
    }
    $ligne->prix          = $acte_caisse->montant_base;
    $ligne->quantite      = $acte_caisse->quantite;
    $ligne->coeff         = $this->_coeff;
    if ($msg = $ligne->store()) {
      return $msg;
    }
  }
  
  /**
   * Ligne de report pour calculer un numéro de BVR pour la facture 
   * 
   * @param string $report l'élément à reporter
   * 
   * @return void
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
   * @return void
  **/
  function getNoControle($noatraiter){
    if (!$noatraiter) {
      $noatraiter = $this->du_patient + $this->du_tiers;
    }
    $noatraiter = str_replace(' ','',$noatraiter);
    $noatraiter = str_replace('-','',$noatraiter);
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
   * @return void
  **/
  function loadNumerosBVR(){
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") && !count($this->_montant_factures_caisse)) {
      $this->_total_tarmed = 0;
      $this->_total_caisse = 0;
      $this->_autre_tarmed = 0;
      if (count($this->_ref_consults)) {
        foreach ($this->_ref_consults as $consult) {
          foreach ($consult->_ref_actes_tarmed as $acte_tarmed) {
            $this->_total_tarmed += $acte_tarmed->montant_base + $acte_tarmed->montant_depassement;
          }
          foreach ($consult->_ref_actes_caisse as $acte_caisse) {
            $this->completeField("type_facture");
            $coeff = "coeff_".$this->type_facture;
            $tarif_acte_caisse = ($acte_caisse->montant_base + $acte_caisse->montant_depassement)*$acte_caisse->_ref_caisse_maladie->$coeff;
            if ($acte_caisse->_ref_caisse_maladie->use_tarmed_bill) {
               $this->_autre_tarmed += $tarif_acte_caisse;
            }
            else {
               $this->_total_caisse +=  $tarif_acte_caisse;
            }
          }
        }
      }
      if (count($this->_ref_sejours)) {
        foreach ($this->_ref_sejours as $sejour) {
          foreach ($sejour->_ref_actes_tarmed as $acte_tarmed) {
            $this->_total_tarmed += $acte_tarmed->montant_base + $acte_tarmed->montant_depassement;
          }
          foreach ($sejour->_ref_actes_caisse as $acte_caisse) {
            $coeff = "coeff_".$this->type_facture;
            $tarif_acte_caisse = ($acte_caisse->montant_base + $acte_caisse->montant_depassement)*$acte_caisse->_ref_caisse_maladie->$coeff;
            if ($acte_caisse->_ref_caisse_maladie->use_tarmed_bill) {
              $this->_autre_tarmed += $tarif_acte_caisse;
            }
            else {
              $this->_total_caisse +=  $tarif_acte_caisse;
            }
          }
        }
      }
      $montant_prem = round($this->_total_tarmed * $this->_coeff + $this->_autre_tarmed, 1);
      $this->_total_caisse = round($this->_total_caisse, 1);
      
      if ($montant_prem < 0) {
        $montant_prem = 0;
      }
      if ($this->_total_tarmed || $this->_autre_tarmed) {
//         $this->_montant_factures_caisse[0] = sprintf("%.2f",$montant_prem);
         $this->_montant_factures_caisse[0] = sprintf("%.2f",$montant_prem - $this->remise);
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
      $adherent2 = str_replace(' ','',$this->_ref_praticien->adherent);
      $adherent2 = str_replace('-','',$adherent2);
      foreach ($this->_montant_factures_caisse as $montant_facture) {
        $montant = sprintf('%010d', $montant_facture*100);
        $cle = $this->getNoControle($genre.$montant);
        $this->_num_bvr[$montant_facture] = $genre.$montant.$cle.">".$this->num_reference."+ ".$adherent2.">";
      } 
    }
    return $this->_num_bvr;
  }

  /**
   * Fonction de création des lignes(items) de la facture lorsqu'elle est cloturée
   * 
   * @param string $object objet référence
   * 
   * @return void
  **/
  function rangeActes($object) {
    $this->addActes($object->_ref_actes_tarmed  , "_ref_actes_tarmed");
    $this->addActes($object->_ref_actes_caisse  , "_ref_actes_caisse");
    $this->addActes($object->_ref_actes_ngap    , "_ref_actes_ngap");
    $this->addActes($object->_ref_actes_ccam    , "_ref_actes_ccam");
  }
  
  /**
   * Fonction de création des lignes(items) de la facture lorsqu'elle est cloturée
   * 
   * @param string $actes les actes
   * @param string $name  le nom de la reférence
   * 
   * @return void
  **/
  function addActes($actes, $name) {
    $tab = $this->$name;
    foreach ($actes as $key => $acte) {
      $tab[$key] = $acte;
    }
    $this->$name = $tab;
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
      $this->creationLignesObject($consult, $consult->_date);
    }
    $this->loadRefsSejour();
    foreach ($this->_ref_sejours as $sejour) {
      foreach ($sejour->_ref_operations as $op) {
        $this->creationLignesObject($op, $op->date);
      }
      $this->creationLignesObject($sejour, $sejour->entree_prevue);
    }
  }
  
  /**
   * Fonction de création des lignes de la facture lorsqu'elle est cloturée à partir d'un objet
   * 
   * @param string $object objet référence
   * @param string $date   date à défaut
   * 
   * @return void
  **/
  function creationLignesObject($object, $date){
    foreach ($object->_ref_actes_tarmed as $acte) {
      $this->creationLigneTarmed($acte, $date);
    }
    foreach ($object->_ref_actes_caisse as $acte) {
      $this->creationLigneCaisse($acte, $date);
    }
    foreach ($object->_ref_actes_ccam as $acte_ccam) {
      $this->creationLigneCCAM($acte, $date);
    }
    foreach ($object->_ref_actes_ngap as $acte_ngap) {
      $this->creationLigneNGAP($acte, $date);
    }
  }
}
