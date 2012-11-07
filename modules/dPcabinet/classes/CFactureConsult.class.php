<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Facture liée à une ou plusieurs consultations
 *
 */
class CFactureConsult extends CMbObject {
  // DB Table key
  var $factureconsult_id = null;
  
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
  var $assurance_base         = null;
  var $assurance_complementaire = null;
  var $send_assur_base        = null;
  var $send_assur_compl       = null;
  var $facture                = null;
  var $ref_accident           = null;
  var $statut_pro             = null;
  var $num_reference          = null;
  
  // Form fields
  var $_nb_factures         = null;
  var $_coeff               = null;
  var $_montant_sans_remise = null;
  var $_montant_avec_remise = null;
  var $_montant_secteur1    = null;
  var $_montant_secteur2    = null;
  var $_montant_total       = null;
  
  var $_du_restant_patient        = null;
  var $_du_restant_tiers          = null;
  var $_reglements_total_patient  = null;
  var $_reglements_total_tiers    = null;
  var $_montant_factures          = array();
  var $_num_bvr                   = array();
  var $_montant_factures_caisse   = array();
  
  // Object References
  var $_ref_patient       = null;
  var $_ref_praticien     = null;
  var $_ref_assurance_base            = null;
  var $_ref_assurance_complementaire  = null;
  var $_ref_consults      = null;
  var $_ref_last_consult  = null;
  var $_ref_first_consult = null;
  var $_ref_reglements    = null;
  var $_ref_reglements_patient = null;
  var $_ref_reglements_tiers   = null;
  
  var $_ref_chir = null;
    
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'factureconsult';
    $spec->key   = 'factureconsult_id';
    return $spec;
  }
    
  /**
   * getBackProps
   * 
   * @return $backProps
  **/
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["reglements"]    = "CReglement object_id";
    $backProps["consultations"] = "CConsultation factureconsult_id";
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
    $props["remise"]      = "currency default|0";
    $props["ouverture"]   = "date notNull";
    $props["cloture"]     = "date";
    $props["du_patient"]  = "currency notNull default|0";
    $props["du_tiers"]    = "currency notNull default|0";
    
    $props["type_facture"]              = "enum notNull list|maladie|accident default|maladie";
    $props["patient_date_reglement"]    = "date";
    $props["tiers_date_reglement"]      = "date";
    $props["npq"]                       = "enum notNull list|0|1 default|0";
    $props["cession_creance"]           = "enum notNull list|0|1 default|0";
    $props["assurance_base"]            = "ref class|CCorrespondantPatient";
    $props["assurance_complementaire"]  = "ref class|CCorrespondantPatient";
    $props["send_assur_base"]           = "bool default|0";
    $props["send_assur_compl"]          = "bool default|0";
    $props["facture"]                   = "enum notNull list|-1|0|1 default|0";
    $props["ref_accident"]              = "text";
    $props["statut_pro"]                = "enum list|chomeur|etudiant|non_travailleur|independant|salarie|sans_emploi";
    $props["num_reference"]             = "str minLength|16 maxLength|27";
    
    $props["_du_restant_patient"]       = "currency";
    $props["_du_restant_tiers"]         = "currency";
    $props["_reglements_total_patient"] = "currency";
    $props["_reglements_total_tiers"]   = "currency";
    $props["_montant_sans_remise"]      = "currency";
    $props["_montant_avec_remise"]      = "currency";
    $props["_montant_secteur1"]         = "currency";
    $props["_montant_secteur2"]         = "currency";
    $props["_montant_total"]            = "currency";
    return $props;
  }
     
  /**
   * updateFormFields
   * 
   * @return void
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = sprintf("FA%08d", $this->_id);
  }
     
  /**
   * loadRefsFwd
   * 
   * @return void
  **/
  function loadRefsFwd(){
    $this->loadRefPatient();
    $this->loadRefsConsults();
    $this->loadRefPraticien();
    $this->loadRefAssurance();
  } 
  
  /**
   * Redéfinition du store
   * 
   * @return void
  **/
  function store() {
    // A vérifier pour le == 0 s'il faut faire un traitement
    if ($this->facture !== '0') {
      foreach ($this->loadBackRefs("consultations") as $_consultation) {
        if ($this->facture == -1 && $_consultation->facture == 1) {
          $_consultation->facture = 0;
          $_consultation->store();
        }
        elseif ($this->facture == 1 && $_consultation->facture == 0) {
          $_consultation->facture = 0;
          $_consultation->store();
        }
      }
    }
    
    // Etat des règlement à propager sur les consultations
    if ($this->fieldModified("patient_date_reglement") || $this->fieldModified("tiers_date_reglement")) {
      foreach ($this->loadBackRefs("consultations") as $_consultation) {
        $_consultation->patient_date_reglement = $this->patient_date_reglement;
        $_consultation->tiers_date_reglement   = $this->tiers_date_reglement;
        
        if ($msg = $_consultation->store()) {
          return $msg;
        }
      }
    }
    
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
  } 
       
  /**
   * Chargement des différentes consultations liées à la facture
   * 
   * @param bool $cache cache
   * 
   * @return void
  **/
  function loadRefsConsults($cache = 1) {
    if (count($this->_ref_consults)) {
      return $this->_ref_consults;
    }
      
    $this->_ref_consults = $this->loadBackRefs("consultations", "consultation_id");
    
    $this->loadRefCoeffFacture();
    
    // Eclatement des factures
    $this->_nb_factures = 1;
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ) {
      $this->eclatementTarmed();
    }
    
    // Chargement des actes de consultations
    foreach ($this->_ref_consults as $_consult) {
      $_consult->loadRefsActes();
      $_consult->loadExtCodesCCAM();
    }
    
    $this->updateMontants();
  
    if (count($this->_ref_consults) > 0) {
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
   * Mise à jour des montant secteur 1, 2 et totaux, utilisés pour la comtpa
   * 
   * @return void
  **/
  function updateMontants() {
    $this->_montant_secteur1 = 0.0;
    $this->_montant_secteur1 = 0.0;
    $this->_montant_total    = 0.0;
    if (CModule::getActive("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ) {
      $this->_montant_secteur1 += $this->_montant_sans_remise;
      $this->_montant_total    += $this->_montant_avec_remise;
    }
    else {
      foreach ($this->_ref_consults as $_consult) {
        $this->_montant_secteur1 += $_consult->secteur1;
        $this->_montant_secteur2 += $_consult->secteur2;
        $this->_montant_total    += $_consult->_somme;
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
      $this->remise = sprintf("%.2f",(10*(($this->du_patient+$this->du_tiers)*$this->_coeff))/100);
    }
    
    // Dans le cas d'un éclatement de facture recherche des consultations
    $facture = new CFactureConsult();
    $where = array();
    $where["patient_id"] = "= '$this->patient_id'";
    $where["ouverture"]  = "= '$this->ouverture'";
    $where["cloture"]    = "= '$this->cloture'";
    $where["type_facture"] = "= '$this->type_facture'";
    $factures = $facture->loadList($where, "factureconsult_id DESC");

    if (count($factures) > 1) {
      foreach ($factures as $_facture) {
        $this->_montant_factures[] = $_facture->du_patient + $_facture->du_tiers - $_facture->remise;
        
        $consult = new CConsultation();
        $consult->patient_id        = $this->patient_id;
        $consult->factureconsult_id = $_facture->_id;
        $consults = $consult->loadMatchingList("consultation_id DESC");
        if ($consults) {
          $this->_ref_consults = $consults;
        }
        $this->_nb_factures ++;
      }
    }
    else {
      $this->_montant_factures   = array();
      $this->_montant_factures[] = $this->du_patient + $this->du_tiers - $this->remise;
      $this->loadNumerosBVR();
    }
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
      
      if (!$this->num_reference && $this->_ref_patient->avs) {
        // Le numéro de référence doit comporter 16 ou 27 chiffres
        $this->num_reference = str_replace(' ','',$this->_ref_patient->avs.$this->_id);
        $this->num_reference = str_replace('.','', $this->num_reference);
        $this->num_reference = sprintf("%027s", $this->num_reference);
        $this->store();
//        $this->num_reference = substr( $this->num_reference, 0, 2)." ". $this->num_reference( $this->num_reference, 2, 5)." ".substr( $this->num_reference, 7, 5)." ".substr( $this->num_reference, 12, 5)." ".substr( $this->num_reference, 17, 5)." ".substr( $this->num_reference, 22, 5);
      }
    }
    return $this->_ref_patient;
  }
   
  /**
   * Chargement du praticien de la facture
   * 
   * @return void
  **/
  function loadRefPraticien(){
    $this->_ref_praticien = $this->loadFwdRef("praticien_id", true);
  }
  
  //Ne pas supprimer cette fonction!
  /**
   * loadRefPlageConsult
   * 
   * @return void
  **/
  function loadRefPlageConsult(){
    
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
      $this->_montant_avec_remise = $this->_montant_sans_remise;
    }
    
    if (!$this->_montant_sans_remise) {
      $this->_montant_sans_remise = $this->du_patient  + $this->du_tiers;
      $this->_montant_avec_remise = $this->_montant_sans_remise - $this->remise;
    }
    
    $this->_ref_reglements = $this->loadBackRefs("reglements", 'date');
        
    $this->_du_restant_patient = $this->du_patient;
    $this->_du_restant_tiers   = $this->du_tiers  ;
    
    // Application du coeff
    // @todo A améliorer
    if ($this->_coeff) {
      $this->_du_restant_patient = $this->_montant_avec_remise  - $this->remise;
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
    $this->loadNumerosBVR();
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
    $this->_ref_assurance_base = $this->loadFwdRef("assurance_base", true);
    $this->_ref_assurance_complementaire = $this->loadFwdRef("assurance_complementaire", true);
        
    return $this->_ref_assurance_base;
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
      $total_tarmed = 0;
      $total_caisse = 0;
      $autre_tarmed = 0;
      foreach ($this->_ref_consults as $consult) {
        $consult->loadRefsActes();
        foreach ($consult->_ref_actes_tarmed as $acte_tarmed) {
          $total_tarmed += $acte_tarmed->montant_base + $acte_tarmed->montant_depassement;
        }
        foreach ($consult->_ref_actes_caisse as $acte_caisse) {
          $coeff = "coeff_".$this->type_facture;
          $tarif_acte_caisse = ($acte_caisse->montant_base + $acte_caisse->montant_depassement)*$acte_caisse->_ref_caisse_maladie->$coeff;
          if ($acte_caisse->_ref_caisse_maladie->use_tarmed_bill) {
             $autre_tarmed += $tarif_acte_caisse;
          }
          else {
             $total_caisse +=  $tarif_acte_caisse;
          }
        }
      }
      $montant_prem = $total_tarmed * $this->_coeff + $autre_tarmed;
      
      if ($montant_prem < 0) {
        $montant_prem = 0;
      }
      if ($total_tarmed) {
         $this->_montant_factures_caisse[] = sprintf("%.2f",$montant_prem - $this->remise);
      }
      if ($total_caisse >0) {
        $this->_montant_factures_caisse[] = $total_caisse;
      }
      
      $this->_montant_sans_remise = sprintf("%.2f",$montant_prem + $total_caisse);
      $this->_montant_avec_remise = $this->_montant_sans_remise - $this->remise;
      if (count($this->_montant_factures) == 1) {
        $this->_montant_factures = $this->_montant_factures_caisse;
      }
      else {
        $this->_montant_factures_caisse = $this->_montant_factures;
      }
    
      if (!$this->_ref_praticien) {
        $this->loadRefPraticien();
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
   * Fonction permettant à partir d'un numéro de référence de retrouver la facture correspondante
   * 
   * @param string $num_reference le numéro de référence 
   * 
   * @return $facture
  **/
  function findFacture($num_reference){
    $facture = new CFactureConsult();
    $facture->num_reference = $num_reference;
    $facture->loadMatchingObject();
    return $facture;
  }
}
