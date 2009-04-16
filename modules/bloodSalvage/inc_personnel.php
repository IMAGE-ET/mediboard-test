<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

function loadAffected(&$blood_salvage_id, &$list_nurse_sspi, &$tabAffected, &$timingAffect) {
	$affectation = new CAffectationPersonnel();
	$affectation->object_class = "CBloodSalvage";
	$affectation->object_id    = $blood_salvage_id;
	$tabAffected = $affectation->loadMatchingList();
	
	foreach($tabAffected as $key=>$affect) {
	  if(array_key_exists($affect->personnel_id, $list_nurse_sspi)){
	    unset($list_nurse_sspi[$affect->personnel_id]);
	  } 
	  $affect->_ref_personnel->loadRefUser();
	}
	
	// Initialisations des tableaux des timings
	foreach($tabAffected as $key=> $affectation){
	  $timingAffect[$affectation->_id]["_debut"] = array();
	  $timingAffect[$affectation->_id]["_fin"] = array();
	}
	
	// Remplissage des tableaux des timings
	foreach($tabAffected as $id => $affectation){
	  foreach($timingAffect[$affectation->_id] as $key => $value){
	    for($i = -10; $i < 10 && $affectation->$key !== null; $i++) {
	      $timingAffect[$affectation->_id][$key][] = mbTime("$i minutes", $affectation->$key);
	    }  
	  }   
	}
}

?>