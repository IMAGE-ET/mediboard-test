<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

/**
 * The CDocGedSuivi class
 */
class CDocGedSuivi extends CMbObject {
  // DB Table key
  var $doc_ged_suivi_id = null;
    
  // DB Fields
  var $user_id    = null;
  var $doc_ged_id = null;
  var $file_id    = null;
  var $remarques  = null;
  var $date       = null;
  var $actif      = null;
  var $etat       = null;
  
  // Object References
  var $_ref_proc    = null;
  var $_ref_user    = null;
  var $_ref_file    = null;

  function CDocGedSuivi() {
    $this->CMbObject("doc_ged_suivi", "doc_ged_suivi_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "user_id"          => "notNull ref class|CMediusers",
      "doc_ged_id"       => "notNull ref class|CDocGed",
      "file_id"          => "ref class|CFile",
      "remarques"        => "notNull text",
      "etat"             => "notNull enum list|0|16|32|48|64",
      "date"             => "dateTime",
      "actif"            => "bool"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->_ref_proc = new CDocGed;
    $this->_ref_proc->load($this->doc_ged_id);
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
  }
  
  function loadProcComplete(){
    // Chargement des procédures Terminées
  }  
  
  function loadHistory($doc_ged_id = null){
    // Chargement de l'historique complet pour une procédure
    if(!$doc_ged_id){
      $doc_ged_id = $this->doc_ged_suivi_id;
    }  
  }
  
  function loadFile(){
    $this->_ref_file = new CFile;
    if ($this->file_id)
      $this->_ref_file->load($this->file_id);
  }
  
  function delete_suivi($doc_ged_id,$lastactif_id){
  	$supprSuivi = new CDocGedSuivi;
    $where = array();
    $where["doc_ged_id"] = "= '$doc_ged_id'";
    if($lastactif_id)
      $where["doc_ged_suivi_id"] = "> '$lastactif_id'";
    $supprSuivi = $supprSuivi->loadList($where);
    // Supression de chacun des enregistrement
    foreach($supprSuivi as $keySuppr=>$currSuppr){
      $supprSuivi[$keySuppr]->delete();
    }
  }
  
  function delete() {
    // Suppression du fichier correspondant
    $msg = null;
    if ($this->file_id){
      $this->loadFile();
      if($this->_ref_file->file_id){
        $this->_ref_file->delete();
      }
    }

    //suppression de la doc
    return parent::delete();
  }
}
?>