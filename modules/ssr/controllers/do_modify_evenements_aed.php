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

$elts_id = explode("|", $token_elts);
foreach($elts_id as $_elt_id){
  $evenement_ssr = new CEvenementSSR();
  $evenement_ssr->load($_elt_id);
  
	// Suppression des evenements SSR
	if($del){
		$msg = $evenement_ssr->delete();
		CAppUI::displayMsg($msg, "CEvenementSSR-msg-delete");
	}
	// Modification des evenements SSR
  else {
	  if($_nb_decalage_min_debut){
	  	$evenement_ssr->debut = mbDateTime("$_nb_decalage_min_debut minutes", $evenement_ssr->debut);
	  }
		if($_nb_decalage_heure_debut){
      $evenement_ssr->debut = mbDateTime("$_nb_decalage_heure_debut hours", $evenement_ssr->debut);
    }
		if($_nb_decalage_jour_debut){
      $evenement_ssr->debut = mbDateTime("$_nb_decalage_jour_debut days", $evenement_ssr->debut);
    }
    if($_nb_decalage_duree){
      $evenement_ssr->duree = $evenement_ssr->duree + $_nb_decalage_duree;
    }
		if($equipement_id){
		  $evenement_ssr->equipement_id = $equipement_id;
    }
		if($kine_id){
      $evenement_ssr->therapeute_id = $kine_id;
    }
		if($realise != ''){
      $evenement_ssr->realise = $realise;
    }
    
    $msg = $evenement_ssr->store();
		CAppUI::displayMsg($msg, "CEvenementSSR-msg-modify");
	}
}

echo CAppUI::getMsg();
CApp::rip();

?>