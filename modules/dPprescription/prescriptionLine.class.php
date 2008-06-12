<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPprescription
 *  @version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescriptionLine class
 */
class CPrescriptionLine extends CMbObject {
  
  // DB Fields
  var $prescription_id  = null;
  var $ald              = null;
  var $praticien_id     = null;
  var $signee           = null;
  
  var $debut            = null;
  var $duree            = null;
  var $unite_duree      = null;
  var $date_arret       = null;
  var $child_id         = null;
  
  var $decalage_line = null;    // Permet de gerer les decalages dans le tps entre les lignes d'un protocole
  var $fin = null;              // Date de fin utilisée pour stocker la fin de la ligne dans le cas de la prescription de sortie
  var $_ref_parent_line = null;
  var $_ref_child_line = null;
  
  var $_ref_log_signee = null;
  var $_ref_log_date_arret = null;
  var $_ref_prises = null;
  var $_fin = null;
  var $_protocole = null;
  var $_count_parent_line = null;
  var $_count_prises_line = null;
  
  
  function getSpecs() {
    $specsParent = parent::getSpecs();
    $specs = array (
      "prescription_id" => "notNull ref class|CPrescription cascade",
      "ald"             => "bool",
      "praticien_id"    => "ref class|CMediusers",
      "signee"          => "bool",
      "debut"           => "date",
      "duree"           => "num",
      "unite_duree"     => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
      "date_arret"      => "date",
      "child_id"        => "ref class|$this->_class_name",
      "decalage_line"   => "num min|0",
      "fin"             => "date",
      "_fin"            => "date moreEquals|debut"
    );
    return array_merge($specsParent, $specs);
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prise_posologie"] = "CPrisePosologie object_id";
    return $backRefs;
  }
  
  function loadRefsPrises(){
    $this->_ref_prises = $this->loadBackRefs("prise_posologie");  
  }
  
  // Chargement de la child_line
  function loadRefChildLine(){
    $this->_ref_child_line = new $this->_class_name;
    if($this->child_id){
      $this->_ref_child_line->_id = $this->child_id;
      $this->_ref_child_line->loadMatchingObject();
    }  
  }
  
  
  function countParentLine(){
    $line = new $this->_class_name;
    $line->child_id = $this->_id;
    $this->_count_parent_line = $line->countMatchingList(); 
  }

  function countPrisesLine(){
    $prise = new CPrisePosologie();
    $prise->object_id = $this->_id;
    $prise->object_class = $this->_class_name;
    $this->_count_prises_line = $prise->countMatchinglist();  
  }
  
  function loadRefParentLine(){
    $this->_ref_parent_line = new $this->_class_name;
    $this->_ref_parent_line->child_id = $this->_id;
    $this->_ref_parent_line->loadMatchingObject();
  }

  // Chargement recursif des parents d'une ligne
  function loadRefsParents($lines = array()) {
    if(!array_key_exists($this->_id, $lines)){
      $lines[$this->_id] = $this;
    }
    // Chargement de la parent_line
    $this->loadRefParentLine();
    // si $this possede une parent_line
    if($this->_ref_parent_line->_id){
      // on stocke la parent_line
      $lines[$this->_ref_parent_line->_id] = $this->_ref_parent_line;
      // on relance la fonction recursive sur la parent_line touvée
      return $this->_ref_parent_line->loadRefsParents($lines);
    } else {
      return $lines;
    }
  }
  

  
  function delete(){
    $old_id = $this->_id;
    
    // Suppression de la ligne
    if($msg = parent::delete()){
      return $msg;
    }
    
     // Chargement de la child_line de l'objet à supprimer
    $line = new $this->_class_name;
     $line->child_id = $old_id;
     $line->loadMatchingObject();
     if($line->_id){
       // On vide le child_id
       $line->child_id = "";
       if($msg = $line->store()){
         return $msg;
       }
     }
   
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefPrescription();
    $this->loadRefChildLine();
    $this->_protocole = ($this->_ref_prescription->object_id) ? "0" : "1";
    
    if($this->duree && $this->debut){
      if($this->unite_duree == "minute" || $this->unite_duree == "heure" || $this->unite_duree == "demi_journee"){
        $this->_fin = $this->debut;
      }
      if($this->unite_duree == "jour"){
        $_duree_temp = mbDate("+ $this->duree DAYS", $this->debut);
        $this->_fin = mbDate(" -1 DAYS", $_duree_temp);  
      }
      if($this->unite_duree == "semaine"){
        $_duree_temp = mbDate("+ $this->duree WEEKS", $this->debut);
        $this->_fin = mbDate(" -1 DAYS", $_duree_temp);
      }
      if($this->unite_duree == "quinzaine"){
        $duree_temp = 2 * $this->duree;
        $this->_fin = mbDate("+ $duree_temp WEEKS", $this->debut);
      }
      if($this->unite_duree == "mois"){
        $this->_fin = mbDate("+ $this->duree MONTHS", $this->debut);  
      }
      if($this->unite_duree == "trimestre"){
        $duree_temp = 3 * $this->duree;
        $this->_fin = mbDate("+ $duree_temp MONTHS", $this->debut);  
      }
      if($this->unite_duree == "semestre"){
        $duree_temp = 6 * $this->duree;
        $this->_fin = mbDate("+ $duree_temp MONTHS", $this->debut);  
      }
      if($this->unite_duree == "an"){
        $this->_fin = mbDate("+ $this->duree YEARS", $this->debut);  
      }
    }
    $this->countParentLine();
    $this->countPrisesLine();
  }
  
  
  function loadRefPrescription(){
    $this->_ref_prescription = new CPrescription();
    $this->_ref_prescription->load($this->prescription_id);
  }
  
  function loadRefPraticien(){
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien->load($this->praticien_id);
  }
  
  function loadRefLogSignee(){
    $this->_ref_log_signee = $this->loadLastLogForField("signee");
  }
  
  
  function loadRefLogDateArret(){   
    $this->_ref_log_date_arret = $this->loadLastLogForField("date_arret");
  }
  
  function duplicateLine($praticien_id, $prescription_id) {

    global $AppUI;
    
    // Chargement de la ligne de prescription
    $new_line = new CPrescriptionLineMedicament();
    $new_line->load($this->_id);
    
    $date_arret_tp = $new_line->date_arret;
    
    $new_line->loadRefsPrises();
    $new_line->loadRefPrescription();
    
    $new_line->_id = "";
    
    // Si date_arret (cas du sejour)
    if($new_line->date_arret){
      $new_line->debut = $new_line->date_arret;
      $new_line->date_arret = "";
      if($new_line->date_arret < $new_line->_fin){
        $new_line->duree = mbDaysRelative($new_line->debut,$new_line->_fin);
      }
    } else {
      $new_line->debut = mbDate();
    }
    
    $new_line->unite_duree = "jour";
    if($new_line->duree < 0){
      $new_line->duree = "";
    }
    $new_line->praticien_id = $praticien_id;
    $new_line->signee = 0;
    $new_line->valide_pharma = 0;
    
    $prescription = new CPrescription();
    $prescription->load($prescription_id);
    
    // Si prescription de sortie, on duplique la ligne en ligne de prescription
    if($prescription->type == "sortie" && $new_line->_traitement && !$date_arret_tp){
      $new_line->prescription_id = $prescription_id;
    }
    
    $msg = $new_line->store();
    
    $AppUI->displayMsg($msg, "msg-CPrescriptionLineMedicament-create");
    
    foreach($new_line->_ref_prises as &$prise){
      // On copie les prises
      $prise->_id = "";
      $prise->object_id = $new_line->_id;
      $prise->object_class = "CPrescriptionLineMedicament";
      $msg = $prise->store();
      $AppUI->displayMsg($msg, "msg-CPrisePosologie-create");
    }
    
    $old_line = new CPrescriptionLineMedicament();
    $old_line->load($this->_id);
    
    if(!($prescription->type == "sortie" && $old_line->_traitement && !$date_arret_tp)){
      $old_line->child_id = $new_line->_id;
      if($prescription->type != "sortie" && !$old_line->date_arret){
        $old_line->date_arret = mbDate();
      }
      $old_line->store();
    }
  }
}

?>