<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$sejour_id            = CValue::post("sejour_id");
$cdarrs               = CValue::post("cdarrs");
$_cdarrs               = CValue::post("_cdarrs");
$equipement_id        = CValue::post("equipement_id");
$therapeute_id        = CValue::post("therapeute_id");
$line_id              = CValue::post("line_id");
$remarque             = CValue::post("remarque");
$seance_collective    = CValue::post("seance_collective");    // Checkbox de la seance collective
$seance_collective_id = CValue::post("seance_collective_id"); // Id de la seance collective

$codes_cdarrs = array();

//@TODO: array_merge
if(is_array($cdarrs)){
	foreach($cdarrs as $_code_cdarr){
	  $codes_cdarrs[] = $_code_cdarr;
	}
}
if(is_array($_cdarrs)){
  foreach($_cdarrs as $_code_cdarr_manuel){
    $codes_cdarrs[] = $_code_cdarr_manuel;
  }
}

$_days = CValue::post("_days");
$_heure = CValue::post("_heure");
$duree = CValue::post("duree");

$kine = new CMediusers();
$kine->load($therapeute_id);

$sejour = new CSejour;
$sejour->load($sejour_id);

// Ajout d'un evenement dans la seance choisie
if($seance_collective_id){
	$evenement_ssr = new CEvenementSSR();
	$evenement_ssr->sejour_id = $sejour_id;
	$evenement_ssr->prescription_line_element_id = $line_id;
	$evenement_ssr->seance_collective_id = $seance_collective_id;
	
	$evenement_ssr->loadMatchingObject();
	if($evenement_ssr->_id){
	  CAppUI::displayMsg("Patient déjà présent dans la séance", "CEvenementSSR-title-create");
	} else {
		$msg = $evenement_ssr->store();
	  CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
	  foreach($codes_cdarrs as $_cdarr){
      $acte_cdarr = new CActeCdARR();
      $acte_cdarr->code = $_cdarr;
      $acte_cdarr->evenement_ssr_id = $evenement_ssr->_id;
      $msg = $acte_cdarr->store();
      CAppUI::displayMsg($msg, "CActeCdARR-msg-create");
    }
	}
} 

// Creation des evenements et eventuellement des seances si la checkbox est cochée
else {
	if(count($_days)){
		foreach($_days as $_day){
	    $entree = mbDate($sejour->entree);
	    $sortie = mbDate($sejour->sortie);
	    if (!in_range($_day, $entree, $sortie)) {
	      CAppUI::setMsg("CEvenementSSR-msg-failed-bounds", UI_MSG_WARNING);
	      continue; 
	    }
	  
	    if (!$_heure || !$duree) {
	      continue;
	    }	
			
			$evenement_ssr = new CEvenementSSR();
		  $evenement_ssr->equipement_id = $equipement_id;
		  $evenement_ssr->debut = "$_day $_heure";
		  $evenement_ssr->duree = $duree;
	    $evenement_ssr->remarque = $remarque;
	    
			// Chargement du remplacant s'il est specifié
			$plage_conge = new CPlageConge();
			$where = array();
	    $where["user_id"] = "= '$therapeute_id'";
	    $where[] = "'$_day' BETWEEN date_debut AND date_fin";
	    $plage_conge->loadObject($where);
	    
			$replacer_id = "";
			if($plage_conge->_id){
				$replacement = new CReplacement();
				$replacement->conge_id = $plage_conge->_id;
				$replacement->sejour_id = $sejour->_id;
				$replacement->loadMatchingObject();
				
				$replacer_id = $replacement->replacer_id;
			}
			$evenement_ssr->therapeute_id = $replacer_id ? $replacer_id : $therapeute_id;
		
		
		  // Si l'evenement n'est pas une seance collective
	    if(!$seance_collective){
	      $evenement_ssr->prescription_line_element_id = $line_id;
	      $evenement_ssr->sejour_id = $sejour_id;
	    }
	
	    // Store de l'evenement ou de la nouvelle seance
			$msg = $evenement_ssr->store();
			CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
			
			$evenement_id_for_cdarr = $evenement_ssr->_id;
						
			// Si une seance a ete créée, on crée l'evenement lié a la seance, et on crée les code cdarr sur l'evenement
      if($seance_collective){
        $evt_ssr = new CEvenementSSR();
        $evt_ssr->sejour_id = $sejour_id;
        $evt_ssr->prescription_line_element_id = $line_id;
        $evt_ssr->seance_collective_id = $evenement_ssr->_id;
        $msg = $evt_ssr->store();
        CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
				
				// Si une seance a ete créée, les codes cdarrs seront créés sur l'evenement de la seance
				$evenement_id_for_cdarr = $evt_ssr->_id;    
      } 
			
      // On applique les codes cdarrs à l'evenement
			foreach($codes_cdarrs as $_cdarr){
        $acte_cdarr = new CActeCdARR();
        $acte_cdarr->code = $_cdarr;
        $acte_cdarr->evenement_ssr_id = $evenement_id_for_cdarr;
        $msg = $acte_cdarr->store();
        CAppUI::displayMsg($msg, "CActeCdARR-msg-create");
      }
	  }
	}
}
echo CAppUI::getMsg();
CApp::rip();

?>