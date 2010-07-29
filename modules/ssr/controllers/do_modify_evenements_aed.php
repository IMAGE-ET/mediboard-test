<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$token_elts               = CValue::post("token_elts");
$del                      = CValue::post("del", 0);
$_nb_decalage_min_debut   = CValue::post("_nb_decalage_min_debut");
$_nb_decalage_heure_debut = CValue::post("_nb_decalage_heure_debut");
$_nb_decalage_jour_debut  = CValue::post("_nb_decalage_jour_debut");
$_nb_decalage_duree       = CValue::post("_nb_decalage_duree");
$kine_id                  = CValue::post("kine_id");
$equipement_id            = CValue::post("equipement_id");
$realise                  = CValue::post("realise");
$sejour_id                = CValue::post("sejour_id");

$elts_id = explode("|", $token_elts);
foreach($elts_id as $_elt_id){
  $evenement = new CEvenementSSR();
  $evenement->load($_elt_id);
	
  // Autres rééducateurs
  global $can;
  if ($evenement->therapeute_id !=  CAppUI::$instance->user_id && !$can->admin) {
    CAppUI::displayMsg("Impossible de modifier les événements d'un autre rééducateur", "CEvenementSSR-msg-modify");
    continue;
  }
	
	// Suppression des evenements SSR
	if($del){
		$seance_collective_id = $evenement->seance_collective_id;
		
		// Suppression de l'evenement
		$msg = $evenement->delete();
    CAppUI::displayMsg($msg, "CEvenementSSR-msg-delete");
			
		if($seance_collective_id){
			$seance = new CEvenementSSR();
			$seance->load($seance_collective_id);
			
			// Suppression de la seance si plus aucune backref
			if($seance->countBackRefs("evenements_ssr") == 0){
        $msg = $seance->delete();
        CAppUI::displayMsg($msg, "CEvenementSSR-msg-delete");
      } 
		}
	}
	// Modification des evenements SSR
  else {
  	if($_nb_decalage_min_debut || $_nb_decalage_heure_debut || $_nb_decalage_jour_debut || $_nb_decalage_duree || $equipement_id || $kine_id) {
  		if($evenement->seance_collective_id){
  			$evenement->loadRefSeanceCollective();
				$evenement =& $evenement->_ref_seance_collective;
  		}
	
	    if($_nb_decalage_min_debut){
	      $evenement->debut = mbDateTime("$_nb_decalage_min_debut minutes", $evenement->debut);
	    }
	    if($_nb_decalage_heure_debut){
	      $evenement->debut = mbDateTime("$_nb_decalage_heure_debut hours", $evenement->debut);
	    }
	    if($_nb_decalage_jour_debut){
	      $evenement->debut = mbDateTime("$_nb_decalage_jour_debut days", $evenement->debut);
	    }
	    if($_nb_decalage_duree){
	      $evenement->duree = $evenement->duree + $_nb_decalage_duree;
	    }
	    if($equipement_id || $equipement_id == 'none'){
	      $evenement->equipement_id = ($equipement_id == 'none') ? "" : $equipement_id;
	    }
	    if($kine_id){
	      $evenement->therapeute_id = $kine_id;
	    }			
      $msg = $evenement->store();
      CAppUI::displayMsg($msg, "CEvenementSSR-msg-modify");	
  	}
		
		// Realisation des actes CdARR
		if($realise != ''){
			if($realise == '0'){
				if(!$evenement->sejour_id){
					$evenement->loadRefsEvenementsSeance();
					foreach($evenement->_ref_evenements_seance as $_evt_seance){
						$_evt_seance->realise = 0;
						$msg = $_evt_seance->store();
            CAppUI::displayMsg($msg, "CEvenementSSR-msg-modify");
					}
				}
			}
      $evenement->realise = $realise;
	    $msg = $evenement->store();
	    CAppUI::displayMsg($msg, "CEvenementSSR-msg-modify");
		}
	}
}

echo CAppUI::getMsg();
CApp::rip();

?>