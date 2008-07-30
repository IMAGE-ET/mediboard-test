<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */


global $AppUI, $can, $m;

$can->needsRead();

$prescription_id = mbGetValueFromPost("prescription_id");
$protocole_id    = mbGetValueFromPost("protocole_id");
$date_sel  = mbGetValueFromPost("debut", mbDate());
$praticien_id    = mbGetValueFromPost("praticien_id", $AppUI->user_id);

if(!$protocole_id){
	exit();
}
// Chargement du protocole
$protocole = new CPrescription();
$protocole->load($protocole_id);

// Chargement des lignes de medicaments, d'elements et de commentaires
$protocole->loadRefsLinesMed();
$protocole->loadRefsLinesElement();
$protocole->loadRefsLinesAllComments();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

$sejour = new CSejour();
if($prescription->_ref_object->_class_name == "CSejour"){
	$sejour =& $prescription->_ref_object;
}

$lines = array();
$lines["medicament"] = $protocole->_ref_prescription_lines;
$lines["element"] = $protocole->_ref_prescription_lines_element;

foreach($lines as $type => $lines_by_type){
	$object_class = ($type == "medicament") ? "CPrescriptionLineMedicament" : "CPrescriptionLineElement";
	
	if(count($lines_by_type)){
		foreach($lines_by_type as $_line){
			$_line->loadRefsPrises();
			$_line->_id = "";
	    $_line->unite_duree = "jour";
			
	    // Calcul de la date d'entree
		  switch($_line->jour_decalage){
		  	case 'E': 
		  	  $date_debut = $sejour->_entree;
		  		break;
		  	case 'I': 
		  	  $date_debut = $date_sel;	
		  		break;
		  	case 'S':  
		  	  $date_debut = $sejour->_sortie;
		  		break;
		  	case 'N':  
		  	  $date_debut = mbDate();	
		  		break;
		  }
		  
		  // Calcul de la date de sortie
		  $signe_fin = ($_line->decalage_line_fin >= 0) ? "+" : "";
		  switch($_line->jour_decalage_fin){
		  	case 'I':
		  		$date_fin = $date_sel;
		  		break;
		  	case 'S':
		  		$date_fin = mbDate($sejour->_sortie);
		  		break;
		  }
		  if($_line->decalage_line_fin){
		    // On ajuste la date de fin avec le decalage
		  	$date_fin = mbDate("$signe_fin $_line->decalage_line_fin DAYS", $date_fin);	
		  }
		  
		  if(!$_line->jour_decalage){
		  	$date_debut = $date_sel;
		  }
		  
	    if($type == "medicament"){
			  $signe = ($_line->decalage_line >= 0) ? "+" : "";
			  if($_line->decalage_line){
			    $_line->debut = mbDate("$signe $_line->decalage_line DAYS", $date_debut);	
			  } else {
			  	$_line->debut = mbDate($date_debut);
			  }
	    }
	
		  if($type == "element"){
			  $chapitre = $_line->_ref_element_prescription->_ref_category_prescription->chapitre;
		    if($chapitre != "dmi"){
			    $signe = ($_line->decalage_line >= 0) ? "+" : "";
			    if($_line->decalage_line){
			      $_line->debut = mbDate("$signe $_line->decalage_line DAYS", $date_debut);	
			    } else {
			    	$_line->debut = mbDate($date_debut);
			    }	
		    }
		  }
	  
		  // Calcul de la duree
		  if($_line->jour_decalage_fin){
		  	$_line->duree = mbDaysRelative($_line->debut, $date_fin);
		  }
		  
		  $_line->prescription_id = $prescription_id;
		  $_line->praticien_id = $praticien_id;
		  $_line->creator_id = $AppUI->user_id;
		  $msg = $_line->store();
		  $AppUI->displayMsg($msg, "$object_class-msg-create");  
		  	
			// Parcours des prises
			foreach($_line->_ref_prises as $prise){
			  $prise->_id = "";
				$prise->object_id = $_line->_id;
				$prise->object_class = $object_class;
			  $msg = $prise->store();
			  $AppUI->displayMsg($msg, "CPrisePosologie-msg-create");  	
			}	  
		}
	}
}

// Parcours des lignes de commentaires
foreach($protocole->_ref_prescription_lines_all_comments as $line_comment){
	$line_comment->_id = "";
	$line_comment->prescription_id = $prescription_id;
	$line_comment->praticien_id = $praticien_id;
	$line_comment->creator_id = $AppUI->user_id;
	$msg = $line_comment->store();
	$AppUI->displayMsg($msg, "CPrescriptionLineComment-msg-create");
}

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription_id)</script>";
echo $AppUI->getMsg();
exit();   

?>