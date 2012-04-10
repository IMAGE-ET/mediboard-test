<?php /* $Id: CFactureConsult.class.php $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author SARL OpenXtrem
* @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*/

class CFactureConsult extends CMbObject {
  
  // DB Table key
  var $factureconsult_id = null;
  
  // DB Fields
  var $patient_id   = null; 
  var $rabais       = null; 
  var $ouverture    = null; 
  var $cloture      = null;
  var $du_patient   = null;
  var $du_tiers     = null;
  var $montant_sans_remise = null;
  var $montant_avec_remise = null;
  var $type_facture = null;
  var $tarif     = null;
  
  // Form fields
  var $_du_patient_restant        = null;
  var $_reglements_total_patient  = null;
  var $_der_consult_id             = null;
  
  // Object References
  var $_ref_patient       = null;
  var $_ref_consults      = null;
  var $_ref_der_consult   = null;
  var $_ref_reglements    = null;
  var $_montant_factures  = array();
  
  var $_ref_chir = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'factureconsult';
    $spec->key   = 'factureconsult_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["reglement"] = "CReglement object_id";
//    $backProps["factures"]  = "CConsultation factureconsult_id";
    return $backProps;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["patient_id"]  = "ref class|CPatient purgeable seekable notNull show|1";
    $props["rabais"]      = "currency";
    $props["ouverture"]   = "date notNull";
    $props["cloture"]     = "date";
    $props["du_patient"]  = "float";
    $props["du_tiers"]    = "float";
    $props["type_facture"]       = "enum notNull list|maladie|accident default|maladie";
    $props["_du_patient_restant"]       = "currency";
    $props["_reglements_total_patient"] = "currency";
    $props["montant_sans_remise"]       = "currency";
    $props["montant_avec_remise"]       = "currency";
    $props["tarif"]                     = "str";
    return $props;
  }
    
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefPatient();
    $this->_view = "Facture de ".$this->_ref_patient->_view;
  }
  
  function loadRefsFwd(){
  	$this->loadRefPatient();
    $this->loadRefsConsults();
  } 
  
  function loadRefsConsults($cache = 1) {
  	$consult = new CConsultation();
  	
  	$where = array();
  	$where["patient_id"] = "= '$this->patient_id'";
  	$where["factureconsult_id"] = "= '$this->factureconsult_id'";
  	$order = "consultation_id DESC";
  	
    $this->_ref_consults = $consult->loadList($where, $order);
    
    //Dans le cas d'un éclatement de facture recherche des consultations
    /*
    $facture = new CFactureConsult();
    
    $where = array();
    $where["patient_id"] = "= '$this->patient_id'";
    $where["ouverture"]  = "= '$this->ouverture'";
    $where["cloture"]    = "= '$this->cloture'";
    $where["type_facture"] = "= '$this->type_facture'";
    
    $factures = $facture->loadList( $where, "factureconsult_id DESC");
    if(count($factures)>1){
      foreach($factures as $fact){
        $this->_montant_factures[] = $fact->du_patient + $fact->du_tiers - $fact->rabais;
        $refs = $consult->loadList("patient_id = '$this->patient_id' AND factureconsult_id = '$fact->factureconsult_id'", "consultation_id DESC");
        if($refs){
          $this->_ref_consults = $refs;
        }
      }
    }
    */
    foreach($this->_ref_consults as $key => $consult){
    	$consult->loadRefsActes();
      $consult->loadExtCodesCCAM();
      if($consult->_count_actes == 0){
      	unset($this->_ref_consults[$key]);
      }
    }
    $this->loadRefsDerConsultation();
  }
  
  function loadRefsDerConsultation() {
    $this->_ref_der_consult = reset($this->_ref_consults);
    if($this->_ref_der_consult){
      $this->_der_consult_id = $this->_ref_der_consult->_id;
    }
    return $this->_ref_der_consult;
  }
  
  function loadRefPatient($cache = 1) {
    return $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
  }  
  
  function loadRefPraticien(){
  	$this->loadRefsConsults();
  	if($this->_ref_der_consult){
	  	$this->_ref_chir = $this->_ref_der_consult->loadRefPraticien();
	  	$this->tarif = $this->_ref_der_consult->tarif;
  	}
  }
  
  function loadRefPlageConsult(){}
	
  function loadRefReglements($cache = 1) {
  	if($this->_montant_factures){
      $this->montant_sans_remise = 0;
	    foreach($this->_montant_factures as $_montant){
	      $this->montant_sans_remise += $_montant;
	    }
  	}
  	else{
  		$this->montant_sans_remise = $this->du_patient  + $this->du_tiers;
  	}
  	$this->montant_avec_remise = $this->montant_sans_remise - $this->rabais;
  	
    $this->_ref_reglements = $this->loadBackRefs("reglement", 'date');
    
    $this->_du_patient_restant = $this->du_patient - $this->rabais;
    foreach($this->_ref_reglements as $_reglement){
      $_reglement->loadRefBanque();
      if($_reglement->emetteur == "patient"){
      	$this->_du_patient_restant  -= $_reglement->montant;
      	$this->_reglements_total_patient += $_reglement->montant;
      }
    }
    return $this->_ref_reglements;
  }
  
  function loadRefsBack(){
  	$this->loadRefReglements();
  }
  
  function loadRefs(){
  	$this->loadRefsFwd();
  	$this->loadRefsBack();
  }
  
	//Ligne de report pour calcul BVR
	function ligneReport($report){
	  $etalon = ('09468271350946827135');
	  $lignereport = substr($etalon, $report, 10);
	  return $lignereport;
	}
	
	//Numéro de contrôle BVR
	function getNoControle($noatraiter){
	  $report = 0;
	  $cpt = strlen($noatraiter);
	  for($i = 0; $i < $cpt; $i++){
      $report = substr($this->lignereport($report), substr($noatraiter, $i, 1), 1);
	  }
	  $report =  (10 - $report) % 10;
	  return $report;
	}
}
?>