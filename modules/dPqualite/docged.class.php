<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision$
 *  @author Sébastien Fillonneau
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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'doc_ged';
    $spec->key   = 'doc_ged_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["documents_ged_suivi"] = "CDocGedSuivi doc_ged_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["group_id"]         = "ref class|CGroups";
    $specs["user_id"]          = "ref class|CMediusers";
    $specs["doc_chapitre_id"]  = "ref class|CChapitreDoc";
    $specs["doc_theme_id"]     = "ref class|CThemeDoc";
    $specs["doc_categorie_id"] = "ref class|CCategorieDoc";
    $specs["titre"]            = "str maxLength|50";
    $specs["etat"]             = "enum notNull list|0|16|32|48|64";
    $specs["version"]          = "currency min|0";
    $specs["annule"]           = "bool";
    $specs["num_ref"]          = "float";
    return $specs;
  }

  function getEtatRedac() {
    $etat = array();
    $etat[self::DEMANDE]   = CAppUI::tr("CDocGed-msg-etatredac_DEMANDE");
    $etat[self::REDAC]     = CAppUI::tr("CDocGed-msg-etatredac_REDAC");
    $etat[self::VALID]     = CAppUI::tr("CDocGed-msg-etatredac_VALID");
    if($this->annule){
      $etat[self::TERMINE] = CAppUI::tr("CDocGed-msg-etat_INDISPO");
    }else{
      $etat[self::TERMINE] = CAppUI::tr("CDocGed-msg-etat_DISPO");
    }
    if($this->etat)
      $this->_etat_actuel = $etat[$this->etat];
  }
  
  function getEtatValid() {
    $etat = array();
    $etat[self::DEMANDE]   = CAppUI::tr("CDocGed-msg-etatvalid_DEMANDE");
    $etat[self::REDAC]     = CAppUI::tr("CDocGed-msg-etatvalid_REDAC");
    $etat[self::VALID]     = CAppUI::tr("CDocGed-msg-etatvalid_VALID");
    if($this->annule){
      $etat[self::TERMINE] = CAppUI::tr("CDocGed-msg-etat_INDISPO");
    }else{
      $etat[self::TERMINE] = CAppUI::tr("CDocGed-msg-etat_DISPO");  
    }
    if($this->etat)
      $this->_etat_actuel = $etat[$this->etat];
  }
    
  function loadRefsFwd() {
    $this->_ref_user = new CMediusers();
    $this->_ref_user->load($this->user_id);
    
    $this->_ref_group = new CGroups;
    $this->_ref_group->load($this->group_id);
    
    $this->_ref_chapitre = new CChapitreDoc;
    if ($this->_ref_chapitre->load($this->doc_chapitre_id)) {
      $this->_ref_chapitre->computePath();
    }
    
    $this->_ref_theme = new CThemeDoc;
    $this->_ref_theme->load($this->doc_theme_id);
    
    $this->_ref_categorie = new CCategorieDoc;
    $this->_ref_categorie->load($this->doc_categorie_id);
    
    if($this->doc_chapitre_id && $this->doc_categorie_id) {
    	$ref = str_pad(sprintf("%3.1f", $this->num_ref), 5, '0', STR_PAD_LEFT);
    	while (strlen($ref) > 3 && ($ref[strlen($ref)-1] == '0' || $ref[strlen($ref)-1] == '.')) {
    		$ref = substr($ref, 0, -1);
    	}
      if(CAppUI::conf("dPqualite CDocGed _reference_doc")) {
        $this->_reference_doc = $this->_ref_categorie->code."-".$this->_ref_chapitre->_path.$ref;
      } else {
        $this->_reference_doc = $this->_ref_chapitre->_path.$this->_ref_categorie->code."-".$ref;
      }
    }
  }
  
  static function loadProc($user_id = null, $where, $annule = null){
    if($user_id){
      $where["user_id"] = "= '$user_id'";
    }
    if($annule !== null){
      $where["annule"] = "= '$annule'";
    }
    $proc = new CDocGed;
    return $proc->loadList($where);
  }
  
  static function loadProcDemande($user_id = null,$annule = null){
    // Chargement des Procédures en cours de demande    
    $where = array();
    $where["etat"] = "= '".self::DEMANDE."'";
    return self::loadProc($user_id,$where,$annule);
  }
  
  static function loadProcTermineOuRefuse($user_id = null,$annule = null){
    $where = array();           
    $where["etat"] = "= '".self::TERMINE."'";
    return self::loadProc($user_id,$where,$annule);
  }

  static function loadProcRedac($user_id = null,$annule = null){
    // Chargement des Procédures en Attente d'upload d'un fichier (Redaction)
    $where = array();           
    $where["etat"] = "= '".self::REDAC."'";
    return self::loadProc($user_id,$where,$annule);
  }
  
  static function loadProcRedacAndValid($user_id = null,$annule = null){
    // Chargement des Procédures en Attente d'upload d'un fichier (Redaction)
    $where = array("(`doc_ged`.etat = '".self::VALID."' || `doc_ged`.etat = '".self::REDAC."')");
    return self::loadProc($user_id,$where,$annule);
  }
  
  function loadLastActif(){
    // Récupération du dernier document Actif
    $this->_lastactif = new CDocGedSuivi;
    $this->_lastactif->doc_ged_id = $this->doc_ged_id;
    $this->_lastactif->actif = 1;
    $this->_lastactif->loadMatchingObject("date DESC");
    $this->_lastactif->loadRefsFwd();
  }
  
  function loadLastEntry(){
    // Récupération derniere entrée
    $this->_lastentry = new CDocGedSuivi;
    $this->_lastentry->doc_ged_id = $this->doc_ged_id;
    $this->_lastentry->loadMatchingObject("date DESC");
    $this->_lastentry->loadRefsFwd();
  }
  
  function loadFirstEntry(){
    // Récupération derniere entrée
    $this->_firstentry = new CDocGedSuivi;
    $this->_firstentry->doc_ged_id = $this->doc_ged_id;
    $this->_firstentry->user_id = $this->user_id;
    $this->_firstentry->etat = self::DEMANDE;
    $this->_firstentry->loadMatchingObject("date DESC");
    $this->_firstentry->loadRefsFwd();
  }
  
  function check() {
    if($this->_id) {
      $oldObj = new CDocGed();
      $oldObj->load($this->_id);
      if($this->group_id === null) {
        $this->group_id = $oldObj->group_id;
      }
      if($this->doc_chapitre_id === null) {
        $this->doc_chapitre_id = $oldObj->doc_chapitre_id;
      }
      if($this->doc_categorie_id === null) {
        $this->doc_categorie_id = $oldObj->doc_categorie_id;
      }
      if($this->num_ref === null) {
        $this->num_ref = $oldObj->num_ref;
      }
      if($this->annule === null) {
        $this->annule = $oldObj->annule;
      }
    }
    if($this->annule == 1) {
      return null;
    }
    $where = array();
    if($this->_id) {
      $where["doc_ged_id"] = "!= '".$this->_id."'";
    }
    $where["num_ref"]          = "IS NOT NULL";
    $where["group_id"]         = "= '".$this->group_id."'";
    $where["doc_chapitre_id"]  = "= '".$this->doc_chapitre_id."'";
    $where["doc_categorie_id"] = "= '".$this->doc_categorie_id."'";
    $where["num_ref"]          = "= '".$this->num_ref."'";
    $where["annule"]           = "= '0'";
    $order = "num_ref DESC";
    $sameNumRef = new self;
    $sameNumRef->loadObject($where, $order);
    if($sameNumRef->_id) {
      return "Un document existe déjà avec la même référence";
    }
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
      return CAppUI::tr("CDocGed-msg-error_delete");
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