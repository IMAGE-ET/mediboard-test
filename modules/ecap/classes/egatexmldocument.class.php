<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $aTypesAnesth;

// tableau associatif pour les types d'anesthésie
// cf clé sur : index.php?m=dPplanningOp&tab=vw_edit_typeanesth
// A quelques "typeanesth_id" on associe un "TypeOFAnesthesia"
$aTypesAnesth = array("7" => "4",
                       "5" => "1",
                       "4" => "3",
                       "1" => "0");

class CEGateXMLDocument extends CMbXMLDocument {
  var $pmsipath               = "modules/ecap/egate";
  var $finalpath              = null;
  var $schemapath             = null;
  var $schemafilename         = null;
  var $documentfilename       = null;
  var $documentfinalprefix    = null;
  var $documentfinalfilename  = null;
  var $sentFiles              = array();
  var $now                    = null;
  var $msgError               = null;

  function __construct($schemaname) {
    parent::__construct();

    $this->schemapath = "$this->pmsipath/$schemaname";
    $this->schemafilename   = "$this->schemapath/schema.xml"  ;
    $this->documentfilename = "$this->schemapath/document.xml";
    $this->finalpath = CFile::$directory . "/egate/$schemaname";

    $this->now = time();
  }

  function checkSchema() {
    if (!is_dir($this->schemapath)) {
      trigger_error("PatientStayInformation schemas are missing. Please extract them from archive in '$this->schemapath/' directory", E_USER_WARNING);
      return false;
    }
    if (!is_file($this->schemafilename)) {
      $schema = new CEGateXMLSchema();
      $schema->importSchemaPackage($this->schemapath);
      $schema->purgeIncludes();
      $schema->save($this->schemafilename);
    }
    return true;
  }

  function addNameSpaces() {
    // Ajout des namespace pour XML Spy
    $this->addAttribute($this->documentElement, "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
    $this->addAttribute($this->documentElement, "xsi:schemaLocation", "http://www.capio.com schema.xml");
  }
  
  function addElement($elParent, $elName, $elValue = null, $elNS = "http://www.capio.com") {
    $elName  = utf8_encode($elName );
    $elValue = utf8_encode($elValue);
    return $elParent->appendChild(new DOMElement($elName, $elValue, $elNS));
  }

  function saveTempFile() {
    parent::save($this->documentfilename);
  }
  
  function saveFinalFile() {
    $this->documentfinalfilename = "$this->finalpath/$this->documentfinalprefix-$this->now.xml";
    CMbPath::forceDir(dirname($this->documentfinalfilename));
    parent::save($this->documentfinalfilename);
  }

  function addOperations($elParent, $mbSejour){
    global $aTypesAnesth;
    $aOperations =& $mbSejour->_ref_operations;
    foreach($aOperations as $operation_id => $operation){
      $surgery = $this->addElement($elParent, "Surgery");
      $this->addAttribute($surgery, "AnesthetistID"      , $operation->anesth_id);

      if($operation->_ref_consult_anesth->consultation_anesth_id && $operation->_ref_consult_anesth->ASA){
        $this->addAttribute($surgery, "ASA"              , $operation->_ref_consult_anesth->ASA);
      }

      if($operation->type_anesth && array_key_exists($operation->type_anesth ,$aTypesAnesth)){
        $this->addAttribute($surgery, "TypeOfAnesthesiaID" , $aTypesAnesth[$operation->type_anesth]);
      }
      $mbSalle_id = CValue::first(
        $operation->_ref_plageop->salle_id,
        $operation->salle_id
      );
      
      $TheatreRoom = $this->addElement($surgery, "TheatreRoom");
      $this->addAttribute($TheatreRoom, "BU_ID"  , $mbSejour->group_id);
      $this->addAttribute($TheatreRoom, "RoomID" , $mbSalle_id);
      
      // Ajout des actes codés
      $code_trouve = 0;
      foreach ($operation->_ref_actes_ccam as $keyActe => $acte) {
        if($acte->code_activite == 1){
          $surgeryProdecure = $this->addElement($surgery, "SurgeryProcedure");
          $this->addAttribute($surgeryProdecure, "SurgeonID"            , $acte->executant_id);
          $SurgeryProcedureCode = $this->addElement($surgeryProdecure   , "SurgeryProcedureCode");
          $this->addAttribute($SurgeryProcedureCode, "ProcedureCodeID"  , $acte->code_acte);
          $code_trouve++;
        }
      }
      if($code_trouve == 0){
        $this->msgError[] = "Il n'y a aucun acte de codé.";
      }
      
      
      $this->addSurgeryTime($surgery, "3", $operation->entree_bloc     , $operation->sortie_reveil , $operation->date);
      $this->addSurgeryTime($surgery, "5", $operation->entree_salle    , $operation->sortie_salle  , $operation->date);
      $this->addSurgeryTime($surgery, "6", $operation->debut_op        , $operation->fin_op        , $operation->date);
      $this->addSurgeryTime($surgery, "7", $operation->entree_reveil   , $operation->sortie_reveil , $operation->date);
      $this->addSurgeryTime($surgery, "8", $operation->induction_debut , $operation->induction_fin , $operation->date);
      
      $AnesthesiaRoom = $this->addElement($surgery, "AnesthesiaRoom");
      $this->addAttribute($AnesthesiaRoom, "BU_ID"  , $mbSejour->group_id);
      $this->addAttribute($AnesthesiaRoom, "RoomID" , $mbSalle_id);
      
      $RecoveryRoom = $this->addElement($surgery, "RecoveryRoom");
      $this->addAttribute($TheatreRoom, "BU_ID"  , $mbSejour->group_id);
      $this->addAttribute($TheatreRoom, "RoomID" , "8"); // Correspond à l'ID de SSPI
    }
    
  }
  
  function addSurgeryTime($elParent, $typeID, $start, $end, $date = null){
    if($start && $end){
      $surgeryTime = $this->addElement($elParent, "SurgeryTime");
      $this->addAttribute($surgeryTime, "TypeID" , $typeID);
      $this->addDateTimeElement($surgeryTime, "Start", $date." ".$start);
      $this->addDateTimeElement($surgeryTime, "End"  , $date." ".$end);
    }
  }

  function addAffectations($elParent, $mbSejour){
    $aSejourService = array();
    $service_id     = null;
    $curr_key       = 0;
    
    foreach($mbSejour->_ref_affectations as $affectation){
      $affectation->loadRefLit();
      $affectation->_ref_lit->loadRefChambre();
      $affectation->_ref_lit->_ref_chambre->loadRefsFwd();
      $curr_service_id = $affectation->_ref_lit->_ref_chambre->_ref_service->service_id;
      
      if($service_id != $curr_service_id){
        $curr_key++;
        $service_id = $curr_service_id;
        $aSejourService[$curr_key] = array("service_id" => $service_id,
                                            "entree" => $affectation->entree,
                                            "sortie" => null);
      }
      $aSejourService[$curr_key]["sortie"] = $affectation->sortie;
    }
    
    foreach($aSejourService as $sejourService){
      $wardStay = $this->addElement($elParent, "WardStay");
      $this->addAttribute($wardStay, "WardID" , $sejourService["service_id"]);
      
      $wardTime = $this->addElement($wardStay, "WardTime");
      $this->addAttribute($wardTime, "TypeID" , 2);
      $this->addDateTimeElement($wardTime, "Start" , $sejourService["entree"]);
      $this->addDateTimeElement($wardTime, "End"   , $sejourService["sortie"]);
    }
  }
}

?>