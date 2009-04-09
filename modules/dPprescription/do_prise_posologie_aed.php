<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$del = mbGetValueFromPost("del");
$moment_unitaire_id = mbGetValueFromPost("moment_unitaire_id");
$mode_checkbox = false;

//Traitement specifique pour la gestion des checkBox
$list_checkbox = array("matin", "midi", "soir");
foreach($list_checkbox as $_checkbox){
  if(isset($_POST[$_checkbox])){
	  $moment_unitaire = new CMomentUnitaire();
	  $moment_unitaire->libelle = "le $_checkbox";
	  $moment_unitaire->loadMatchingObject();
	  
	  $prise_poso = new CPrisePosologie();
	  $prise_poso->object_id = mbGetValueFromPost("object_id");
	  $prise_poso->object_class = mbGetValueFromPost("object_class");
	  $prise_poso->unite_prise = mbGetValueFromPost("unite_prise");
	  $prise_poso->quantite = mbGetValueFromPost("quantite");
	  $prise_poso->moment_unitaire_id = $moment_unitaire->_id;
	  $msg = $prise_poso->store();
	  $mode_checkbox = true;
  }
}

if($mode_checkbox){
  CApp::rip();
}



if($del || !$moment_unitaire_id){
	$do = new CDoObjectAddEdit("CPrisePosologie", "prise_posologie_id");
	$do->doIt();
}


$_moment_explode = explode("-",$moment_unitaire_id);

// Si moment unitaire, on reprend un traitement normal
if($_moment_explode[0] == "unitaire"){
  $_POST["moment_unitaire_id"] = $_moment_explode[1];
	$do = new CDoObjectAddEdit("CPrisePosologie", "prise_posologie_id");
	$do->doIt();
} else {
	// On recupere toutes les valeurs passées
  $object_id = mbGetValueFromPost("object_id");
  $object_class = mbGetValueFromPost("object_class");
  $quantite = mbGetValueFromPost("quantite");
  $unite_prise = mbGetValueFromPost("unite_prise");
  $nb_tous_les = mbGetValueFromPost("nb_tous_les");
  $unite_tous_les = mbGetValueFromPost("unite_tous_les");
  $decalage_prise = mbGetValueFromPost("decalage_prise");
  
  // Si moment complexe, chargement des moments unitaires correspondants
  $moment = new CBcbMoment();
  $moment->load($_moment_explode[1]);
  $moment->loadRefsAssociations();
  foreach($moment->_ref_associations as &$_association){  	
		$prise_posologie = new CPrisePosologie();
		$prise_posologie->object_id = $object_id;
		$prise_posologie->object_class = $object_class;
		$prise_posologie->moment_unitaire_id = $_association->moment_unitaire_id;
		// Si association ne OR, quantite à 0
		if($_association->OR){
			$prise_posologie->quantite = 0;
		} else {
		  $prise_posologie->quantite = $quantite;
		}
		$prise_posologie->unite_prise = $unite_prise;
		$prise_posologie->nb_tous_les = $nb_tous_les;
		$prise_posologie->unite_tous_les = $unite_tous_les;
		$prise_posologie->decalage_prise = $decalage_prise;
		
		if($msg = $prise_posologie->store()){
			return $msg;
		}
  }
  CApp::rip();
}
?>