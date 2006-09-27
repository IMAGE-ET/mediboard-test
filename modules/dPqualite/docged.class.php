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

if(!defined("CDOC_DEMANDE")) {
  define("CDOC_MODELE"     , 0);
  define("CDOC_DEMANDE"    , 16);
  define("CDOC_REDAC"      , 32);
  define("CDOC_VALID"      , 48);
  define("CDOC_TERMINE"    , 64);
}
 
class CDocGed extends CMbObject {
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
  
  function CDocGed() {
    $this->CMbObject("doc_ged", "doc_ged_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "group_id"         => "ref|notNull",
      "user_id"          => "ref",
      "doc_chapitre_id"  => "ref",
      "doc_theme_id"     => "ref",
      "doc_categorie_id" => "ref",
      "titre"            => "str|maxLength|50",
      "etat"             => "enum|0|16|32|48|64|notNull",
      "version"          => "currency|min|0",
      "annule"           => "enum|0|1",
      "num_ref"          => "num"
    );
    $this->_props =& $props;

    static $seek = array (
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
  }

  function getEtatRedac() {
    $etat = array();
    $etat[CDOC_DEMANDE]   = "Demande en cours de traitement";
    $etat[CDOC_REDAC]     = "En Attente de Rédaction";
    $etat[CDOC_VALID]     = "En Cours de Validation";
    if($this->annule){
      $etat[CDOC_TERMINE]   = "Document Non Disponible";
    }else{
      $etat[CDOC_TERMINE]   = "Document Disponible";  
    }
    if($this->etat)
      $this->_etat_actuel = $etat[$this->etat];
  }
  
  function getEtatValid() {
    $etat = array();
    $etat[CDOC_DEMANDE]   = "Demande de Procédure";
    $etat[CDOC_REDAC]     = "En Cours de Rédaction";
    $etat[CDOC_VALID]     = "En Attente de Validation";
    if($this->annule){
      $etat[CDOC_TERMINE]   = "Document Non Disponible";
    }else{
      $etat[CDOC_TERMINE]   = "Document Disponible";  
    }
    if($this->etat)
      $this->_etat_actuel = $etat[$this->etat];
  }
    
  function loadRefsBack() {
    // Forward references
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
    
    $this->_ref_chapitre = new CChapitreDoc;
    if($this->doc_chapitre_id)
      $this->_ref_chapitre->load($this->doc_chapitre_id);
      
    $this->_ref_theme = new CThemeDoc;
    if ($this->doc_theme_id)
      $this->_ref_theme->load($this->doc_theme_id);
      
    $this->_ref_categorie = new CCategorieDoc;
    if ($this->doc_categorie_id)
      $this->_ref_categorie->load($this->doc_categorie_id);
    
    if($this->doc_chapitre_id && $this->doc_categorie_id){
      $this->_reference_doc = $this->_ref_chapitre->code . "-" . $this->_ref_categorie->code . "-" . str_pad($this->num_ref, 3, "0", STR_PAD_LEFT);
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
    $where["etat"] = "= '".CDOC_DEMANDE."'";
    return $this->loadProc($user_id,$where,$annule);
  }
  
  function loadProcTermineOuRefuse($user_id = null,$annule = null){
    $where = array();           
    $where["etat"] = "= '".CDOC_TERMINE."'";
    return $this->loadProc($user_id,$where,$annule);
  }

  function loadProcRedac($user_id = null,$annule = null){
    // Chargement des Procédures en Attente d'upload d'un fichier (Redaction)
    $where = array();           
    $where["etat"] = "= '".CDOC_REDAC."'";
    return $this->loadProc($user_id,$where,$annule);
  }
  
  function loadProcRedacAndValid($user_id = null,$annule = null){
    // Chargement des Procédures en Attente d'upload d'un fichier (Redaction)
    $where = array();
    $where[] = "(`doc_ged`.etat = '".CDOC_VALID."' || `doc_ged`.etat = '".CDOC_REDAC."')";
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
    $where["etat"] = "= '".CDOC_DEMANDE."'";
    $order = "date DESC";
    $this->_firstentry->loadObject($where, $order);
    $this->_firstentry->loadRefsFwd();
  }
  
  function canDelete(&$msg, $oid = null) {
    // Suppr si Demande, redac, valid ou refus de demande (Terminé sans doc actif)
    if($this->etat==CDOC_DEMANDE 
       || $this->etat==CDOC_REDAC 
       || $this->etat==CDOC_VALID
       || $this->etat==CDOC_MODELE
       || ($this->etat==CDOC_TERMINE && !$this->_lastactif->doc_ged_suivi_id)
       || ($this->etat==CDOC_TERMINE && $this->_lastactif->doc_ged_suivi_id!=$this->_lastentry->doc_ged_suivi_id)
       ){
      return true;
    }else{
      $msg = "Cette procédure ne peut pas être supprimée.";
      return false;
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
    if (!$this->canDelete( $msg )) {
      return $msg;
    }    
    $this->_lastactif->delete_suivi($this->doc_ged_id, $this->_lastactif->doc_ged_suivi_id);
    if($this->_lastactif->doc_ged_suivi_id){
      $this->etat = CDOC_TERMINE;
      $this->user_id = 0;
      $this->store(); 
    }else{
      return parent::delete();
    }    
  }  
}
?>