<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * The CRPU class
 * Résumé de Passage aux Urgences
 */
class CRPU extends CMbObject {
  // DB Table key
  var $rpu_id = null;
    
  // DB Fields
  var $sejour_id       = null;
  var $motif_entree    = null;
  var $diag_infirmier  = null;
  var $pec_transport   = null;
  var $pec_douleur     = null;
  var $motif           = null;
  var $ccmu            = null;
  var $gemsa           = null;
  var $orientation     = null;
  var $radio_debut     = null;
  var $radio_fin       = null;
  var $bio_depart      = null;
  var $bio_retour      = null;
  var $specia_att      = null;
  var $specia_arr      = null;
  var $mutation_sejour_id = null;
  var $box_id          = null;
  var $sortie_autorisee = null;
  var $date_at         = null;
  var $circonstance    = null;
  
  // Legacy Sherpa fields
  var $type_pathologie = null; // Should be $urtype
  var $urprov = null;
  var $urmuta = null;
  var $urtrau = null;
  
  // Form fields
  var $_libelle_circonstance = null;
  
  // Distant Fields
  var $_attente           = null;
  var $_presence          = null;
  var $_can_leave         = null;
  var $_can_leave_since   = null;
  var $_can_leave_about   = null;
  var $_can_leave_level   = null;

  // Patient
  var $_patient_id = null;
  var $_cp         = null;
  var $_ville      = null;
  var $_naissance  = null;
  var $_sexe       = null;
  
  // Sejour
  var $_responsable_id = null;
  var $_annule         = null;
  var $_entree         = null;
  var $_DP             = null;
  var $_ref_actes_ccam = null;
  var $_service_id     = null;
  var $_UHCD           = null;
  var $_etablissement_sortie_id = null;
  var $_etablissement_entree_id = null;
  var $_service_entree_id       = null;
  var $_service_sortie_id       = null;
  
  
  // Object References
  var $_ref_sejour = null;
  var $_ref_consult = null;
  var $_ref_sejour_mutation = null;
  
  // Behaviour fields
  var $_bind_sejour = null;
  var $_sortie          = null;
  var $_mode_entree     = null;
  var $_mode_sortie     = null;
  var $_date_at         = null;
  var $_provenance      = null;
  var $_destination     = null;
  var $_transport       = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'rpu';
    $spec->key   = 'rpu_id';
    $spec->measureable = true;
    /*$spec->events = array(
      "accueil" => array(
        "reference1" => array("CSejour",  "sejour_id"),
        "reference2" => array("CPatient", "sejour_id.patient_id"),
      ),
    );*/
    return $spec;
  }

  function getProps() {
    $specsParent = parent::getProps();
    $specs = array (
      "sejour_id"        => "ref notNull class|CSejour cascade",
      "motif_entree"     => "text helped",
      "diag_infirmier"   => "text helped",
      "pec_douleur"      => "text helped",
      "pec_transport"    => "enum list|med|paramed|aucun",
      "motif"            => "text helped",
      "ccmu"             => "enum list|1|P|2|3|4|5|D",
      "gemsa"            => "enum list|1|2|3|4|5|6",
      "type_pathologie"  => "enum list|C|E|M|P|T",
      "orientation"      => "enum list|HDT|HO|SC|SI|REA|UHCD|MED|CHIR|OBST|FUGUE|SCAM|PSA|REO",
      "radio_debut"      => "dateTime",
      "radio_fin"        => "dateTime",
      "bio_depart"       => "dateTime",
      "bio_retour"       => "dateTime",
      "specia_att"       => "dateTime",
      "specia_arr"       => "dateTime",
      "mutation_sejour_id" => "ref class|CSejour",
      "box_id"           => "ref class|CLit",
      "sortie_autorisee" => "bool",
      "date_at"          => "date",
      "circonstance"     => "str",
    
      "_DP"              => "code cim10 show|1",
      "_provenance"      => "enum list|1|2|3|4|5|6|7|8",
      "_destination"     => "enum list|1|2|3|4|6|7",
      "_transport"       => "enum list|perso|perso_taxi|ambu|ambu_vsl|vsab|smur|heli|fo notNull",
      "_mode_entree"     => "enum list|6|7|8 notNull",
      "_mode_sortie"     => "enum list|6|7|8|9 default|8",
      "_sortie"          => "dateTime",
      "_patient_id"      => "ref notNull class|CPatient",
      "_responsable_id"  => "ref notNull class|CMediusers",
      "_service_id"      => "ref".(CAppUI::conf("dPplanningOp CSejour service_id_notNull") == 1 ? ' notNull' : '')." class|CService seekable",
      "_UHCD"            => "bool",
      "_entree"          => "dateTime",
      "_etablissement_sortie_id"        => "ref class|CEtabExterne autocomplete|nom",
      "_etablissement_entree_id" => "ref class|CEtabExterne autocomplete|nom", 
      "_service_entree_id" => "ref class|CService autocomplete|nom dependsOn|group_id|cancelled", 
      "_service_sortie_id"        => "ref class|CService autocomplete|nom dependsOn|group_id|cancelled",
      "_attente"           => "time",
      "_presence"          => "time",
      "_can_leave"         => "time",
      "_can_leave_about"   => "bool",
      "_can_leave_since"   => "bool",
      "_can_leave_level"   => "enum list|ok|warning|error",
     );
     
    $specs["urprov"] = "";
    $specs["urmuta"] = "";
    $specs["urtrau"] = "";    

    // Legacy Sherpa fields
    if (CModule::getActive("sherpa")) {
        $urgDro = new CSpUrgDro();
        $specs["urprov"] = $urgDro->_props["urprov"] . " notNull";
        $specs["urmuta"] = $urgDro->_props["urmuta"];
        $specs["urtrau"] = $urgDro->_props["urtrau"];    
    }

    return array_merge($specsParent, $specs);
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["passages"] = "CRPUPassage rpu_id";
    return $backProps;
  }

  function updateFormFields() {
    parent::updateFormFields();
    
    // @todo: A supprimer du updateFormFields
    $sejour = $this->loadRefSejour();

    $this->_responsable_id = $sejour->praticien_id;
    $this->_entree         = $sejour->_entree;
    $this->_DP             = $sejour->DP;
    $this->_annule         = $sejour->annule;
    $this->_UHCD           = $sejour->UHCD;
    
    $patient =& $sejour->_ref_patient;
    
    $this->_patient_id = $patient->_id;
    $this->_cp         = $patient->cp;
    $this->_ville      = $patient->ville;
    $this->_naissance  = $patient->naissance;
    $this->_sexe       = $patient->sexe;
    $this->_view       = "RPU du " . mbDateToLocale(mbDate($this->_entree)). " pour $patient->_view";
    
    // Calcul des valeurs de _mode_sortie
    if ($sejour->mode_sortie == "mutation") {
      $this->_mode_sortie = 6;
    }
    
    if ($sejour->mode_sortie == "transfert") {
      $this->_mode_sortie = 7; 
    }
    
    if ($sejour->mode_sortie == "normal") {
      $this->_mode_sortie = 8;
    }
    
    if ($sejour->mode_sortie == "deces") {
      $this->_mode_sortie = 9;
    }
    
    $this->_mode_entree             = $sejour->mode_entree;
    $this->_sortie                  = $sejour->sortie_reelle;
    $this->_provenance              = $sejour->provenance;
    $this->_transport               = $sejour->transport;
    $this->_destination             = $sejour->destination;
    $this->_etablissement_sortie_id = $sejour->etablissement_sortie_id;
    $this->_etablissement_entree_id = $sejour->etablissement_entree_id;
    $this->_service_entree_id       = $sejour->service_entree_id;
    $this->_service_sortie_id       = $sejour->service_sortie_id;
    
    // @todo: A supprimer du updateFormFields
    $this->loadRefConsult();
    if ($this->_ref_consult->_id) {
      $this->_ref_consult->countDocItems();
    }
    
    // Récupération du libellé de la circonstance si actif dans la configuration
    if (CAppUI::conf("dPurgences gerer_circonstance")) {
      $this->getCirconstance();
    }
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefSejour();
  }
  
  /**
   * Chargement du séjour
   * @return CSejour
   */
  function loadRefSejour() {
    $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
    $this->_ref_sejour->loadRefsFwd();

    // Calcul des temps d'attente et présence
    $entree = mbTime($this->_ref_sejour->_entree);
    $this->_presence =  mbSubTime($entree, mbTime());

    if ($this->_ref_sejour->sortie_reelle) {
      $this->_presence  = mbSubTime($entree, mbTime($this->_ref_sejour->sortie_reelle));
    }
    
    return $this->_ref_sejour;
  }
  
  function loadRefConsult() {
    // Chargement de la consultation ATU
    if (!$this->_ref_sejour) {
      $this->loadRefSejour();
    }
    
    $sejour =& $this->_ref_sejour;
    $sejour->loadRefsConsultations();
    $this->_ref_consult = $this->_ref_sejour->_ref_consult_atu;

    // Calcul du l'attente       
    $this->_attente  = $this->_presence;
    if ($this->_ref_consult->_id) {
      $entree = mbTime($this->_ref_sejour->_entree);
      $this->_attente  = mbSubTime(mbTransformTime($entree, null,"%H:%M:00"), mbTransformTime(mbTime($this->_ref_consult->heure), null, "%H:%M:00"));
    }

    $this->_can_leave_level = $sejour->sortie_reelle ? "" : "ok";
    if (!$sejour->sortie_reelle) {
      if (!$this->_ref_consult->_id) {
        $this->_can_leave_level = "warning";
      }
      
      // En consultation 
      if ($this->_ref_consult->chrono != 64) {
        $this->_can_leave = -1;
        $this->_can_leave_level = "warning";
      } 
      else {
        if (mbTime($sejour->sortie_prevue) > mbTime()) {
          $this->_can_leave_since = true;
          $this->_can_leave = mbTimeRelative(mbTime(), mbTime($sejour->sortie_prevue));
        } 
        else {
          $this->_can_leave_about = true;
          $this->_can_leave = mbTimeRelative(mbTime($sejour->sortie_prevue), mbTime());
        }
        
        if (CAppUI::conf("dPurgences rpu_warning_time") < $this->_can_leave) {
          $this->_can_leave_level = "warning";          
        }
            
        if (CAppUI::conf("dPurgences rpu_warning_time") < $this->_can_leave) {
          $this->_can_leave_level = "error";          
        }
      }
    }
  }
  
  function loadRefSejourMutation() {
    $this->_ref_sejour_mutation = $this->loadFwdRef("mutation_sejour_id", true);
    $this->_ref_sejour_mutation->loadNDA();
  }
  
  function bindSejour() {
    if (!$this->_bind_sejour) {
      return;
    }
       
    $this->_bind_sejour = false;
    
    $this->loadRefsFwd();
    $sejour =& $this->_ref_sejour;
    $sejour->patient_id = $this->_patient_id;
    $sejour->group_id = CGroups::loadCurrent()->_id;
    $sejour->praticien_id = $this->_responsable_id;
    $sejour->type = "urg";
    $sejour->entree_prevue = $this->_entree;
    $sejour->entree_reelle = $this->_entree;
    $sejour->sortie_prevue = (CAppUI::conf("dPurgences sortie_prevue") == "h24") ? mbDateTime("+1 DAY", $this->_entree) : mbDate(null, $this->_entree)." 23:59:59";
    $sejour->annule        = $this->_annule;    
    $sejour->service_id    = $this->_service_id;
    $sejour->etablissement_entree_id = $this->_etablissement_entree_id;
    $sejour->service_entree_id = $this->_service_entree_id;
    $sejour->mode_entree = $this->_mode_entree;
    $sejour->provenance  = $this->_provenance;
    $sejour->destination = $this->_destination;
    $sejour->transport   = $this->_transport;
    $sejour->UHCD        = $this->_UHCD;
    // Le patient est souvent chargé à vide ce qui pose problème
    // dans le onAfterStore(). Ne pas supprimer.
    $sejour->_ref_patient = null;
    
    if ($msg = $sejour->store()) {
      return $msg;
    }
    
    // Affectation du sejour_id au RPU
    $this->sejour_id = $sejour->_id;
  }
  
  function store() {
    if (!$this->_id) {
      $sejour                = new CSejour();
      $sejour->patient_id    = $this->_patient_id;
      $sejour->type          = "urg";
      $sejour->entree_reelle = $this->_entree;
      $sejour->group_id      = CGroups::loadCurrent()->_id;
            
      $sortie_prevue         = CAppUI::conf("dPurgences sortie_prevue") == "h24" ? 
        mbDateTime("+1 DAY", $this->_entree) : 
        mbDate(null, $this->_entree)." 23:59:59";
      $sejour->sortie_prevue = $this->_sortie ? $this->_sortie : $sortie_prevue;

      // En cas de ressemblance à quelques heures près (cas des urgences), on a affaire au même séjour
      $siblings = $sejour->getSiblings(CAppUI::conf("dPurgences sibling_hours"), $sejour->type);
      if (count($siblings)) {
        $sibling = reset($siblings);
        $this->sejour_id = $sibling->_id;
        $this->loadRefSejour();
        $this->_ref_sejour->loadRefRPU();
        
        // Si y'a un RPU déjà existant on alerte d'une erreur 
        if ($this->_ref_sejour->_ref_rpu->_id) {
          return CAppUI::tr("CRPU-already-exists");
        }
        $this->_bind_sejour = false;
      }
    }
    
    // Changement suivant le mode d'entrée
    switch ($this->_mode_entree) {
      case 6:
        $this->_etablissement_entree_id = "";
        break;
      case 7:
        $this->_service_entree_id = "";
        break;
      case 8:
        $this->_service_entree_id = "";
        $this->_etablissement_entree_id = "";
        break;
    }
 
    // Bind Sejour
    if ($msg = $this->bindSejour()) {
      return $msg;
    }
     
    // Synchronisation AT
    $this->loadRefConsult();
    
    if ($this->_ref_consult->_id && $this->fieldModified("date_at") && !$this->_date_at) {
      $this->_date_at = true;
      $this->_ref_consult->date_at = $this->date_at;
      if ($msg = $this->_ref_consult->store()) {
        return $msg;
      } 
    }
    
    // Standard Store
    if ($msg = parent::store()){
      return $msg;
    }
    
    // Déclenchement pour avoir les données RPU
    // Pas de sycnhro dans certains cas
    $this->_ref_sejour->_no_synchro = true;
    $this->_ref_sejour->notify("AfterStore");
  }
  
  function loadComplete() {
    parent::loadComplete();
    
    $this->_ref_sejour->loadComplete();
  }

  function getCirconstance() {
    $ds = $this->_spec->ds;
    
    $module_orumip = CModule::getActive("orumip");
    $orumip_active = $module_orumip && $module_orumip->mod_active;
    
    $request = new CRequest;
    $request->addSelect("libelle");
    
    if ($orumip_active) {
      $request->addTable("orumip_circonstance");
      $request->addWhere("code = '".$this->circonstance."'");
      $this->_libelle_circonstance = $ds->loadResult($request->getRequest());
    }
    else {
      $request->addTable("circonstance");
      $request->addWhere("code = '".$this->circonstance."'");
      $this->_libelle_circonstance = $ds->loadResult($request->getRequest());
    }
  }
  
  function fillLimitedTemplate(&$template) {
    $this->loadRefConsult();
    $this->_ref_consult->loadRefPraticien();
    
    $this->notify("BeforeFillLimitedTemplate", $template);
    
    // Duplication des champs de la consultation
    $template->addProperty("RPU - Consultation - Praticien nom"    , $this->_ref_consult->_ref_praticien->_user_first_name);
    $template->addProperty("RPU - Consultation - Praticien prénom" , $this->_ref_consult->_ref_praticien->_user_last_name);
    $template->addProperty("RPU - Consultation - Motif"            , $this->_ref_consult->motif);
    $template->addProperty("RPU - Consultation - Remarques"        , $this->_ref_consult->rques);
    $template->addProperty("RPU - Consultation - Examen"           , $this->_ref_consult->examen);
    $template->addProperty("RPU - Consultation - Traitement"       , $this->_ref_consult->traitement);
    
    $template->addProperty("RPU - Diagnostic infirmier"         , $this->diag_infirmier);
    $template->addProperty("RPU - Prise en charge douleur"      , $this->pec_douleur);
    $template->addProperty("RPU - PeC Transport"                , $this->getFormattedValue("pec_transport"));
    $template->addProperty("RPU - Motif"                        , $this->motif);
    $template->addProperty("RPU - CCMU"                         , $this->getFormattedValue("ccmu"));
    $template->addProperty("RPU - Code GEMSA"                   , $this->getFormattedValue("gemsa"));
    $template->addDateTimeProperty("RPU - Départ Radio"         , $this->radio_debut);
    $template->addDateTimeProperty("RPU - Retour Radio"         , $this->radio_fin);
    $template->addDateTimeProperty("RPU - Dépôt Biologie"       , $this->bio_depart);
    $template->addDateTimeProperty("RPU - Réception Biologie"   , $this->bio_retour);
    $template->addDateTimeProperty("RPU - Attente spécialiste"  , $this->specia_att);
    $template->addDateTimeProperty("RPU - Arrivée spécialiste"  , $this->specia_arr);
    $template->addProperty("RPU - Accident du travail"          , $this->getFormattedValue("date_at"));
    $libelle_at = $this->date_at ? "Accident du travail du " . $this->getFormattedValue("date_at") : "";
    $template->addProperty("RPU - Libellé accident du travail"  , $libelle_at);
    $template->addProperty("RPU - Sortie autorisée"             , $this->getFormattedValue("sortie_autorisee"));
    
    $lit = new CLit;
    if ($this->box_id) {
      $lit->load($this->box_id);
    }
    $template->addProperty("RPU - Box"                          , $lit->_view);
    
    if(CAppUI::conf("dPurgences old_rpu") == "1"){
      if (CModule::getActive("sherpa")) {
        $template->addProperty("RPU - Soins pour trauma"  , $this->getFormattedValue("urtrau"));
        $template->addProperty("RPU - Cause du transfert" , $this->getFormattedValue("urmuta"));
      }
      $template->addProperty("RPU - Type de pathologie"   , $this->getFormattedValue("type_pathologie"));
    } 
    else {
      $template->addProperty("RPU - Orientation"          , $this->getFormattedValue("orientation"));
    }
    
    $this->notify("AfterFillLimitedTemplate", $template);
  }
  
  function completeLabelFields(&$fields) {
    $sejour = $this->loadRefSejour();
    $sejour->completeLabelFields($fields);
    $patient = $sejour->loadRefPatient();
    $patient->completeLabelFields($fields);
  }
  
  function docsEditable() {
    return true;
  }
}
?>