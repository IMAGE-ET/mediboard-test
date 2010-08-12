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
$_heure_deb = CValue::post("_heure_deb");
$duree = CValue::post("duree");

$kine = new CMediusers();
$kine->load($therapeute_id);

$sejour = new CSejour;
$sejour->load($sejour_id);

// Ajout d'un evenement dans la seance choisie
if($seance_collective_id){
	$evenement = new CEvenementSSR();
	$evenement->sejour_id = $sejour_id;
	$evenement->prescription_line_element_id = $line_id;
	$evenement->seance_collective_id = $seance_collective_id;
	
	$evenement->loadMatchingObject();
	if($evenement->_id){
	  CAppUI::displayMsg("Patient déjà présent dans la séance", "CEvenementSSR-title-create");
	} else {
		$msg = $evenement->store();
	  CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
	  foreach($codes_cdarrs as $_cdarr){
      $acte_cdarr = new CActeCdARR();
      $acte_cdarr->code = $_cdarr;
      $acte_cdarr->evenement_ssr_id = $evenement->_id;
      $msg = $acte_cdarr->store();
      CAppUI::displayMsg($msg, "CActeCdARR-msg-create");
    }
	}
} 

// Creation des evenements et eventuellement des seances si la checkbox est cochée
else {
	if(count($_days)){
    $sejour->loadRefBilanSSR();
    $entree = mbDate($sejour->entree);
    $sortie = mbDate($sejour->sortie);

		$bilan =& $sejour->_ref_bilan_ssr;
		$bilan->loadRefKineReferent();
		$referant =& $bilan->_ref_kine_referent;

		foreach($_days as $_day){
	    if (!in_range($_day, $entree, $sortie)) {
	      CAppUI::setMsg("CEvenementSSR-msg-failed-bounds", UI_MSG_WARNING);
	      continue; 
	    }
	  
	    if (!$_heure_deb || !$duree) {
	      continue;
	    }	
			
			$evenement = new CEvenementSSR();
		  $evenement->equipement_id = $equipement_id;
		  $evenement->debut         = "$_day $_heure_deb";
		  $evenement->duree         = $duree;
	    $evenement->remarque      = $remarque;
			$evenement->therapeute_id = $therapeute_id;
	          		
      // Transfert kiné référent => kiné remplaçant si disponible
      if ($therapeute_id == $referant->_id) {
        $conge = new CPlageConge();
        $conge->loadFor($therapeute_id, $_day);
				// Référent en congés
        if ($conge->_id){
          $replacement = new CReplacement();
          $replacement->conge_id = $conge->_id;
          $replacement->sejour_id = $sejour->_id;
          $replacement->loadMatchingObject();
          if ($replacement->_id) {
            $evenement->therapeute_id = $replacement->replacer_id;
          }
        }
      }

      // Transfert kiné remplacant => kiné référant si présent
      if ($sejour->isReplacer($therapeute_id)) {
        $conge = new CPlageConge();
        $conge->loadFor($referant->_id, $_day);
        // Référent présent
        if (!$conge->_id){
          $evenement->therapeute_id = $referant->_id;
        }
      }

		  // Si l'evenement n'est pas une seance collective
	    if(!$seance_collective){
	      $evenement->prescription_line_element_id = $line_id;
	      $evenement->sejour_id = $sejour_id;
	    }
	
	    // Store de l'evenement ou de la nouvelle seance
			$msg = $evenement->store();
			CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
			
			$evenement_id_for_cdarr = $evenement->_id;
						
			// Si une seance a ete créée, on crée l'evenement lié a la seance, et on crée les code cdarr sur l'evenement
      if ($seance_collective){
        $evt_ssr = new CEvenementSSR();
        $evt_ssr->sejour_id = $sejour_id;
        $evt_ssr->prescription_line_element_id = $line_id;
        $evt_ssr->seance_collective_id = $evenement->_id;
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