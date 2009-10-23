<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$token_elt          = mbGetValueFromPost("token_elt");
$prescription_id    = mbGetValueFromPost("prescription_id");
$debut              = mbGetValueFromPost("debut");
$time_debut         = mbGetValueFromPost("time_debut");
$duree              = mbGetValueFromPost("duree");
$unite_duree        = mbGetValueFromPost("unite_duree", "jour");
$quantite           = mbGetValueFromPost("quantite");
$nb_fois            = mbGetValueFromPost("nb_fois");
$unite_fois         = mbGetValueFromPost("unite_fois");
$moment_unitaire_id = mbGetValueFromPost("moment_unitaire_id");
$nb_tous_les        = mbGetValueFromPost("nb_tous_les");
$unite_tous_les     = mbGetValueFromPost("unite_tous_les");
$jour_decalage      = mbGetValueFromPost("jour_decalage");
$decalage_line      = mbGetValueFromPost("decalage_line");
$mode_protocole     = mbGetValueFromPost("mode_protocole","0");
$mode_pharma        = mbGetValueFromPost("mode_pharma","0");
$jour_decalage_fin  = mbGetValueFromPost("jour_decalage_fin");
$decalage_line_fin  = mbGetValueFromPost("decalage_line_fin");
$time_fin           = mbGetValueFromPost("time_fin"); 
$decalage_prise     = mbGetValueFromPost("decalage_prise");
$praticien_id = mbGetValueFromPost("praticien_id", $AppUI->user_id);
$commentaire = mbGetValueFromPost("commentaire");

// Initialisation des tableaux
$lines = array();
$elements = array();

// Explode des listes d'elements et de medicaments
if($token_elt){
  $elements    = explode("|",$token_elt);
}

// Ajout des elements dans la prescription
foreach($elements as $element_id){
	$line_element = new CPrescriptionLineElement();
	$line_element->element_prescription_id = $element_id;
	$line_element->prescription_id = $prescription_id;
	$line_element->praticien_id = $praticien_id;
	$line_element->creator_id = $AppUI->user_id;
  $line_element->commentaire = $commentaire;
	$msg = $line_element->store();
	$AppUI->displayMsg($msg, "CPrescriptionLineElement-msg-create");
	$lines[$line_element->_ref_element_prescription->_ref_category_prescription->chapitre][$line_element->_id] = $line_element;
}

if ($moment_unitaire_id) {
	$_moment_explode = explode("-",$moment_unitaire_id);
	$moment_unitaire_id = $_moment_explode[1];
}

foreach($lines as $cat_name => $lines_by_cat){
	foreach($lines_by_cat as $_line){
		if($cat_name != "dmi"){
      $_line->debut = $debut;
      $_line->time_debut = $time_debut;
	    $_line->jour_decalage = $jour_decalage;
	    $_line->decalage_line = $decalage_line;
    	$_line->time_debut = $time_debut;
            
      if($cat_name != "anapath" && $cat_name != "imagerie" && $cat_name != "consult"){
		    $_line->duree = $duree;
		    $_line->unite_duree = $unite_duree;
		    $_line->decalage_line_fin = $decalage_line_fin;
		    $_line->jour_decalage_fin = $jour_decalage_fin;
		    $_line->time_fin = $time_fin;
      }
	
		  $_line->store();
		  
		  if($cat_name != "anapath" && $cat_name != "imagerie" && $cat_name != "consult"){
		  	if($moment_unitaire_id){
        	// Cas d'un moment complexe
        	if($_moment_explode[0] == "complexe"){
            $moment = new CBcbMoment();
            $moment->load($moment_unitaire_id);
            $moment->loadRefsAssociations();
	        	foreach($moment->_ref_associations as &$_association){  	
							$prise_posologie = new CPrisePosologie();
							$prise_posologie->object_id = $_line->_id;
							$prise_posologie->object_class = $_line->_class_name;
							$prise_posologie->moment_unitaire_id = $_association->moment_unitaire_id;
							// Si association ne OR, quantite à 0
							if($_association->OR){
								$prise_posologie->quantite = 0;
							} else {
							  $prise_posologie->quantite = $quantite;
							}
				      // On sauvegarde par defaut la premiere unite de prise trouvée
			        if($cat_name == "medicament"){
						    $prise_posologie->unite_prise = reset($_line->_unites_prise);
			        }
							$prise_posologie->nb_tous_les = $nb_tous_les;
							$prise_posologie->unite_tous_les = $unite_tous_les;
							$prise_posologie->decalage_prise = $decalage_prise;
							$msg = $prise_posologie->store();
							$AppUI->displayMsg($msg, "CPrisePosologie-msg-create");
						}
        	} else {
        		$prise_posologie = new CPrisePosologie();
						$prise_posologie->object_id = $_line->_id;
						$prise_posologie->object_class = $_line->_class_name;
							
	        	// Prise Moment
						if($quantite && $moment_unitaire_id && !$nb_tous_les){
						  $prise_posologie->quantite = $quantite;
						  $prise_posologie->moment_unitaire_id = $moment_unitaire_id;
						  $msg = $prise_posologie->store();
						  $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");
						}
						// Prise Tous Les
				    if($quantite && $nb_tous_les && $unite_tous_les){
					    $prise_posologie->quantite = $quantite;
					    $prise_posologie->nb_tous_les = $nb_tous_les;
					    $prise_posologie->unite_tous_les = $unite_tous_les;
					    $prise_posologie->moment_unitaire_id = $moment_unitaire_id;
					    $prise_posologie->decalage_prise = $decalage_prise;
					    $msg = $prise_posologie->store();  	
				      $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");
				    } 
        	}
        } 
       	// Cas d'un moment unitaire
				else {
					$prise = new CPrisePosologie();
				  $prise->object_id = $_line->_id;
				  $prise->object_class = $_line->_class_name;	
					
				  // On sauvegarde par defaut la premiere unite de prise trouvée
	        if($cat_name == "medicament"){
				    $prise->unite_prise = reset($_line->_unites_prise);
	        }
     
					// Prise Fois Par
				  if($quantite && $nb_fois && $unite_fois){
					  $prise->quantite = $quantite;
					  $prise->nb_fois = $nb_fois;
					  $prise->unite_fois = $unite_fois;
					  $msg = $prise->store(); 
				 	  $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");
				  }
				  // Prise Tous Les
				  if($quantite && $nb_tous_les && $unite_tous_les){
					  $prise->quantite = $quantite;
					  $prise->nb_tous_les = $nb_tous_les;
					  $prise->unite_tous_les = $unite_tous_les;
					  $prise->moment_unitaire_id = $moment_unitaire_id;
					  $prise->decalage_prise = $decalage_prise;
					  $msg = $prise->store();  	
				    $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");
				  } 
			  } 
       }
		  
		}
	}
}

$lite = !CAppUI::pref('mode_readonly') ? 0 : 1;

// Reload en full mode
if($mode_protocole || $mode_pharma){
  echo "<script type='text/javascript'>window.opener.Prescription.reload('$prescription_id','','','$mode_protocole','$mode_pharma', null, false)</script>";
} else {
  echo "<script type='text/javascript'>window.opener.Prescription.reloadPrescSejour('$prescription_id', null, null, null, null, null, null, true, '$lite')</script>";
}
    
echo $AppUI->getMsg();
CApp::rip();
?>