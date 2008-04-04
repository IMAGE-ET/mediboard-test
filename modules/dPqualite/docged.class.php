<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CDocGed class
 */
 
class CDocGed extends CMbObject {
  const MODELE   = 0;
  const DEMANDE  = 16;
  const REDAC    = 32;
  const VALID    = 48;
  const TERMINE  = 64;

  // DB Table key
  var $doc_ged_id = null;
    
  // DB Fields
  var $group_id         = null;
  var $doc_chapitre_id  = null;
  var $doc_theme_id     = null;
  var $doc_categorie_id = null;
  var $titre            = null;
  var $etat             = null;
  var $version          = null;
  var $user_id          = null;
  var $annule           = null;
  var $num_ref          = null;
  // 
  var $_reference_doc   = null;
  var $_etat_actuel     = null;
  var $_lastentry       = null;
  var $_lastactif       = null;
  var $_firstentry      = null;

  // Object References
  var $_ref_last_doc    = null;
  var $_ref_chapitre    = null;
  var $_ref_theme       = null;
  var $_ref_categorie   = null;
  var $_ref_history     = null;
  var $_ref_group       = null;
  var $_ref_user        = null;
  
  function CDocGed() {
    $this->CMbObject("doc_ged", "doc_ged_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["documents_ged_suivi"] = "CDocGedSuivi doc_ged_id";
     return $backRefs;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "group_id"         => "ref class|CGroups",
      "user_id"          => "ref class|CMediusers",
      "doc_chapitre_id"  => "ref class|CChapitreDoc",
      "doc_theme_id"     => "ref class|CThemeDoc",
      "doc_categorie_id" => "ref class|CCategorieDoc",
      "titre"            => "str maxLength|50",
      "etat"             => "notNull enum list|0|16|32|48|64",
      "version"          => "currency min|0",
      "annule"           => "bool",
      "num_ref"          => "num"
    );
    return array_merge($specsParent, $specs);
  }

  function getEtatRedac() {
    global $AppUI;
    $etat = array();
    $etat[self::DEMANDE]   = CAppUI::tr("msg-CDocGed-etatredac_DEMANDE");
    $etat[self::REDAC]     = CAppUI::tr("msg-CDocGed-etatredac_REDAC");
    $etat[self::VALID]     = CAppUI::tr("msg-CDocGed-etatredac_VALID");
    if($this->annule){
      $etat[self::TERMINE]   = CAppUI::tr("msg-CDocGed-etat_INDISPO");
    }else{
      $etat[self::TERMINE]   = CAppUI::tr("msg-CDocGed-etat_DISPO");  
    }
    if($this->etat)
      $this->_etat_actuel = $etat[$this->etat];
  }
  
  function getEtatValid() {
    global $AppUI;
    $etat = array();
    $etat[self::DEMANDE]   = CAppUI::tr("msg-CDocGed-etatvalid_DEMANDE");
    $etat[self::REDAC]     = CAppUI::tr("msg-CDocGed-etatvalid_REDAC");
    $etat[self::VALID]     = CAppUI::tr("msg-CDocGed-etatvalid_VALID");
    if($this->annule){
      $etat[self::TERMINE]   = CAppUI::tr("msg-CDocGed-etat_INDISPO");
    }else{
      $etat[self::TERMINE]   = CAppUI::tr("msg-CDocGed-etat_DISPO");  
    }
    if($this->etat)
      $this->_etat_actuel = $etat[$this->etat];
  }
    
  function loadRefsFwd() {
    global $dPconfig;
    
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
    
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
    
    $this->_ref_chapitre = new CChapitreDoc;
    if($this->doc_chapitre_id) {
      $this->_ref_chapitre->load($this->doc_chapitre_id);
      $this->_ref_chapitre->computePath();
    }
    $this->_ref_theme = new CThemeDoc;
    if ($this->doc_theme_id) {
      $this->_ref_theme->load($this->doc_theme_id);
    }
    $this->_ref_categorie = new CCategorieDoc;
    if ($this->doc_categorie_id) {
      $this->_ref_categorie->load($this->doc_categorie_id);
    }
    if($this->doc_chapitre_id && $this->doc_categorie_id) {
      if($dPconfig["dPqualite"]["CDocGed"]["_reference_doc"]) {
        $this->_reference_doc = $this->_ref_categorie->code . "-" . $this->_ref_chapitre->_path . str_pad($this->num_ref, 3, "0", STR_PAD_LEFT);
      } else {
        $this->_reference_doc = $this->_ref_chapitre->_path . $this->_ref_categorie->code . "-" . str_pad($this->num_ref, 3, "0", STR_PAD_LEFT);
      }
    }
  }
  
  function loadProc($user_id = null,$where,$annule = null){
    
    if($user_id){
      $where["user_id"] = "= '$user_id'";
    }
    if($annule !== null){
      $where["annule"] = "= '$annule'";
    }
    $procDemandee = new CDocGed;
    $procDemandee = $procDemandee->loadList($where);
    return $procDemandee;
  }
  
  function loadProcDemande($user_id = null,$annule = null){
    // Chargement des Procédures en cours de demande    
    $where = array();
    $where["etat"] = "= '".self::DEMANDE."'";
    return $this->loadProc($user_id,$where,$annule);
  }
  
  function loadProcTermineOuRefuse($user_id = null,$annule = null){
    $where = array();           
    $where["etat"] = "= '".self::TERMINE."'";
    return $this->loadProc($user_id,$where,$annule);
  }

  function loadProcRedac($user_id = null,$annule = null){
    // Chargement des Procédures en Attente d'upload d'un fichier (Redaction)
    $where = array();           
    $where["etat"] = "= '".self::REDAC."'";
    return $this->loadProc($user_id,$where,$annule);
  }
  
  function loadProcRedacAndValid($user_id = null,$annule = null){
    // Chargement des Procédures en Attente d'upload d'un fichier (Redaction)
    $where = array();
    $where[] = "(`doc_ged`.etat = '".self::VALID."' || `doc_ged`.etat = '".self::REDAC."')";
    return $this->loadProc($user_id,$where,$annule);
  }
  
  function loadLastActif(){
    // Récupération du dernier document Actif
    $this->_lastactif = new CDocGedSuivi;
    $where = array();
    $where["doc_ged_id"] = "= '$this->doc_ged_id'";
    $where["actif"] = "= '1'";
    $order = "date DESC";
    $this->_lastactif->loadObject($where, $order);
    $this->_lastactif->loadRefsFwd();
  }
  
  function loadLastEntry(){
    // Récupération derniere entrée
    $this->_lastentry = new CDocGedSuivi;
    $where = array();
    $where["doc_ged_id"] = "= '$this->doc_ged_id'";
    $order = "date DESC";
    $this->_lastentry->loadObject($where, $order);
    $this->_lastentry->loadRefsFwd();
  }
  
  function loadFirstEntry(){
    // Récupération derniere entrée
    $this->_firstentry = new CDocGedSuivi;
    $where = array();
    $where["doc_ged_id"] = "= '$this->doc_ged_id'";
    $where["user_id"] = "= '$this->user_id'";
    $where["etat"] = "= '".self::DEMANDE."'";
    $order = "date DESC";
    $this->_firstentry->loadObject($where, $order);
    $this->_firstentry->loadRefsFwd();
  }
  
  function canDeleteEx() {
    // Suppr si Demande, redac, valid ou refus de demande (Terminé sans doc actif)
    if($this->etat==self::DEMANDE 
       || $this->etat==self::REDAC 
       || $this->etat==self::VALID
       || $this->etat==self::MODELE
       || ($this->etat==self::TERMINE && !$this->_lastactif->doc_ged_suivi_id)
       || ($this->etat==self::TERMINE && $this->_lastactif->doc_ged_suivi_id!=$this->_lastentry->doc_ged_suivi_id)
       ){
      return parent::canDeleteEx();
    }else{
      return CAppUI::tr("msg-CDocGed-error_delete");
    }
  }
  
  function delete() {
    // Suppression ou non de document en cours de modification
    $this->load($this->doc_ged_id);
    if(!$this->_lastactif){
      $this->loadLastActif();
    }
    if(!$this->_lastentry){
      $this->loadLastEntry();
    }
    $msg = null;
    if ($msg = $this->canDeleteEx()) {
      return $msg;
    }    
    $this->_lastactif->delete_suivi($this->doc_ged_id, $this->_lastactif->doc_ged_suivi_id);
    if($this->_lastactif->doc_ged_suivi_id){
      $this->etat = self::TERMINE;
      $this->user_id = 0;
      $this->store(); 
    }else{
      return parent::delete();
    }    
  }  
}
?>