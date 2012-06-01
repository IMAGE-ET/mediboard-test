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
  var $remise       = null; 
  var $ouverture    = null; 
  var $cloture      = null;
  var $du_patient   = null;
  var $du_tiers     = null;
  var $type_facture           = null;
  var $patient_date_reglement = null;
  var $tiers_date_reglement   = null;
  var $tarif                  = null;
  var $npq                    = null;
  var $cession_creance        = null;
  
  // Form fields
  var $_nb_factures         = null;
  var $_coeff               = null;
  var $_montant_sans_remise = null;
  var $_montant_avec_remise = null;
  var $_du_patient_restant        = null;
  var $_du_tiers_restant          = null;
  var $_reglements_total_patient  = null;
  var $_reglements_total_tiers    = null;
  var $_der_consult_id            = null;
  var $_montant_factures          = array();
  var $_num_reference             = null;
  var $_num_bvr                   = array();
  var $_montant_factures_caisse   = array();
  
  // Object References
  var $_ref_patient       = null;
  var $_ref_consults      = null;
  var $_ref_der_consult   = null;
  var $_ref_reglements    = null;
  
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
    $props["remise"]      = "currency default|0";
    $props["ouverture"]   = "date notNull";
    $props["cloture"]     = "date";
    $props["du_patient"]  = "float notNull default|0";
    $props["du_tiers"]    = "float notNull default|0";
    $props["type_facture"]              = "enum notNull list|maladie|accident default|maladie";
    $props["patient_date_reglement"]    = "date";
    $props["tiers_date_reglement"]      = "date";
    $props["npq"]                       = "enum notNull list|0|1 default|0";
    $props["cession_creance"]           = "enum notNull list|0|1 default|0";
    
    $props["_du_patient_restant"]       = "currency";
    $props["_du_tiers_restant"]         = "currency";
    $props["_reglements_total_patient"] = "currency";
    $props["_reglements_total_tiers"]   = "currency";
    $props["_montant_sans_remise"]      = "currency";
    $props["_montant_avec_remise"]      = "currency";
    $props["_num_reference"]            = "str";
    $props["tarif"]                     = "str";
    return $props;
  }
    
  function updateFormFields() {
    parent::updateFormFields();
    if(!$this->_ref_patient){$this->loadRefPatient();}
    $this->_view = "Facture de ".$this->_ref_patient->_view;
  }
  
  function loadRefsFwd(){
  	$this->loadRefPatient();
    $this->loadRefsConsults();
    $this->loadRefPraticien();
  } 
  
  function loadRefsConsults($cache = 1) {
  	$consult = new CConsultation();
  	
  	$where = array();
  	$where["patient_id"]        = "= '$this->patient_id'";
  	$where["factureconsult_id"] = "= '$this->factureconsult_id'";
  	$order = "consultation_id ASC";
  	
    $this->_ref_consults = $consult->loadList($where, $order);
    $this->_nb_factures = 1 ;
    if(CModule::getInstalled("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ){
    
	    if($this->npq){
	      $this->remise = sprintf("%.2f",(10*(($this->du_patient+$this->du_tiers)*$this->_coeff))/100) ;
	    }
	    //Dans le cas d'un éclatement de facture recherche des consultations
	    $facture = new CFactureConsult();
	        
	    $where = array();
	    $where["patient_id"] = "= '$this->patient_id'";
	    $where["ouverture"]  = "= '$this->ouverture'";
	    $where["cloture"]    = "= '$this->cloture'";
	    $where["type_facture"] = "= '$this->type_facture'";
	    
	    $factures = $facture->loadList( $where, "factureconsult_id DESC");
	    if(count($factures)>1){
	      foreach($factures as $fact){
	      	$ajout = $fact->du_patient + $fact->du_tiers - $fact->remise;
	        $this->_montant_factures[] = $ajout;
	        $refs = $consult->loadList("patient_id = '$this->patient_id' AND factureconsult_id = '$fact->factureconsult_id'", "consultation_id DESC");
	        if($refs){
	          $this->_ref_consults = $refs;
	        }
	        $this->_nb_factures ++;
	      }
	    }
	    else{
	    	$this->_montant_factures   = array();
	    	$this->_montant_factures[] = ($this->du_patient + $this->du_tiers) * $this->_coeff - $this->remise;
	    }
    }
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
    $this->_ref_der_consult = end($this->_ref_consults);
    if($this->_ref_der_consult){
      $this->_der_consult_id = $this->_ref_der_consult->_id;
    }
    return $this->_ref_der_consult;
  }
  
  function loadRefPatient($cache = 1) {
    $this->_ref_patient = $this->loadFwdRef("patient_id", $cache);
    
    // Le numéro de référence doit comporter au minimum 16 chiffres
    $this->_num_reference = $this->_ref_patient->matricule.sprintf("%06s",$this->_id);
    return $this->_ref_patient;
  }  
  
  function loadRefPraticien(){
  	if(!$this->_ref_consults){$this->loadRefsConsults();}
  	if($this->_ref_der_consult){
	  	$this->_ref_chir = $this->_ref_der_consult->loadRefPraticien();
	  	$this->tarif     = $this->_ref_der_consult->tarif;
  	}
  }
  
  //Ne pas supprimer cette fonction!
  function loadRefPlageConsult(){}
	
  function loadRefReglements($cache = 1) {
  	if (!CModule::getInstalled("tarmed") || !CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ){
	    $this->_montant_sans_remise = 0;
	    if(count($this->_montant_factures)>1){
	      foreach($this->_montant_factures as $_montant){
	        $this->_montant_sans_remise += $_montant;
	      }
	      $this->_montant_avec_remise = $this->_montant_sans_remise;
	    }
	    else{
	      $this->_montant_sans_remise = $this->du_patient  + $this->du_tiers;
	      $this->_montant_avec_remise = $this->_montant_sans_remise - $this->remise;
	    }
  	}
  	
    $this->_ref_reglements = $this->loadBackRefs("reglement", 'date');
    
    $this->_du_patient_restant = $this->du_patient - $this->remise;
    $this->_du_tiers_restant = $this->du_tiers;
    foreach($this->_ref_reglements as $_reglement){
      $_reglement->loadRefBanque();
      if($_reglement->emetteur == "patient"){
      	$this->_du_patient_restant  -= $_reglement->montant;
      	$this->_reglements_total_patient += $_reglement->montant;
      }
      if($_reglement->emetteur == "tiers"){
      	$this->_du_tiers_restant  -= $_reglement->montant;
      	$this->_reglements_total_tiers += $_reglement->montant;
      }
    }
    return $this->_ref_reglements;
  }
  
  function loadRefsBack(){
  	$this->loadRefReglements();
  }
  
  function loadRefs(){
  	$this->loadRefCoeffFacture();
  	$this->loadRefsFwd();
  	$this->loadRefsBack();
    $this->loadNumerosBVR();
  }
  
  function loadRefCoeffFacture() {
    $this->_coeff = 1;
    if (CModule::getInstalled("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ){
      if ($this->type_facture == "accident"){
        $this->_coeff = CAppUI::conf("tarmed CCodeTarmed pt_accident");
      }
      else{
        $this->_coeff = CAppUI::conf("tarmed CCodeTarmed pt_maladie");
      }
    }
  }
  
	//Ligne de report pour calcul BVR
	function ligneReport($report){
	  $etalon = ('09468271350946827135');
	  $lignereport = substr($etalon, $report, 10);
	  return $lignereport;
	}
	
	//Numéro de contrôle BVR
	function getNoControle($noatraiter){
		if(!$noatraiter){
			$noatraiter = $this->du_patient + $this->du_tiers;
		}
		$noatraiter = str_replace(' ','',$noatraiter);
    $noatraiter = str_replace('-','',$noatraiter);
	  $report = 0;
	  $cpt = strlen($noatraiter);
	  for($i = 0; $i < $cpt; $i++){
      $report = substr($this->lignereport($report), substr($noatraiter, $i, 1), 1);
	  }
	  $report =  (10 - $report) % 10;
	  return $report;
	}
	
	function loadNumerosBVR($select = "_id"){
		if (CModule::getInstalled("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ){
		  $total_tarmed = 0;
		  $total_caisse = array();
				  
			$caisse = new CCaisseMaladie();
			$caisses_maladie = $caisse->loadList(null, "nom");
			foreach($caisses_maladie as $caisse){
				$total_caisse[$caisse->$select] = 0;
			}
			
		  foreach($this->_ref_consults as $consult){
		    $consult->loadRefsActes();
		    foreach($consult->_ref_actes_tarmed as $acte_tarmed){
		      $total_tarmed += $acte_tarmed->montant_base + $acte_tarmed->montant_depassement;
		    }
		    foreach($consult->_ref_actes_caisse as $acte_caisse){
		      $total_caisse[$acte_caisse->_ref_caisse_maladie->$select] += $acte_caisse->montant_base + $acte_caisse->montant_depassement;
		    }
		  }
		  $montant_prem = $total_tarmed * $this->_coeff;
		  
		  if($montant_prem < 0){
		  	$montant_prem = 0;
		  }
		  
	    $this->_montant_factures_caisse[] = sprintf("%.2f",$montant_prem - $this->remise);
	    $this->_montant_sans_remise = sprintf("%.2f",$montant_prem);
		  foreach($total_caisse as $cle => $caisse){
		  	if($caisse){
	        $this->_montant_factures_caisse[$cle] = sprintf("%.2f", $caisse);
	        $this->_montant_sans_remise += sprintf("%.2f",$caisse);
		  	}
		  }
	    $this->_montant_avec_remise = $this->_montant_sans_remise - $this->remise;
		  if(count($this->_montant_factures) == 1){
		  	$this->_montant_factures = $this->_montant_factures_caisse;
		  }
		  else{
		  	$this->_montant_factures_caisse = $this->_montant_factures;
		  }
	    $genre = "01";
			if($this->_ref_chir){
		    $adherent2 = str_replace(' ','',$this->_ref_chir->compte);
		    $adherent2 = str_replace('-','',$adherent2);
			
		    foreach($this->_montant_factures_caisse as $montant_facture){
			    $montant = sprintf('%010d', $montant_facture*100);
			    $cle = $this->getNoControle($genre.$montant);
		      $this->_num_bvr[$montant_facture] = $genre.$montant.$cle.">".$this->_num_reference."+ ".$adherent2.">";
		    }
			}
		}
		return $this->_num_bvr;
	}
}
?>