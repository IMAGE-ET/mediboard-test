<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$prescription_id = CValue::post("prescription_id");
$praticien_id = CValue::post("praticien_id", $AppUI->user_id);
$type = CValue::post("type");
$ajax = CValue::post("ajax");

// Chargement de la prescription à dupliquer
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefObject();
$prescription->loadRefsLinesMed();
$prescription->loadRefsLinesElement();

$lines = array();
$lines["medicament"] = $prescription->_ref_prescription_lines;
$lines["element"] = $prescription->_ref_prescription_lines_element;

$sejour = $prescription->_ref_object;

// Creation de la nouvelle prescription
$prescription->_id = "";
$prescription->type = $type;

$msg = $prescription->store();
CAppUI::displayMsg($msg, "CPrescription-msg-create");
if($msg){
	echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription_id)</script>";
	echo CAppUI::getMsg();
  CApp::rip();
}

// Parcours des lignes de medicaments
foreach($lines as $cat => $lines_by_type){
	foreach($lines_by_type as &$line){
		if($line->_class_name == "CPrescriptionLineElement"){
		  $line_chapter = $line->_ref_element_prescription->_ref_category_prescription->chapitre;
	    // Si element de type DMI, on ne le copie pas
		  if($line_chapter == "dmi"){
		    continue;
	    }
		}
		
		// Chargements des prises
	  $line->loadRefsPrises();

	  // Ligne de pre_adm vers ligne de sejour
	  if($type == "sejour"){
	    // Si la fin n'est pas indiquée ou si la fin est avant l'entree du sejour
	    if((!$line->_fin_reelle && (mbDate($line->_debut_reel) < mbDate($sejour->_entree))) || mbDate($line->_fin_reelle) <= mbDate($sejour->_entree)){
	      continue;
	    }
	    // Modification des dates  
	  	if($line->debut && $line->duree && ($line->debut < mbDate($sejour->_entree))){
				$diff_duree = mbDaysRelative($line->debut, mbDate($sejour->_entree));
				if( $line->duree - $diff_duree > 0){
					$line->duree = $line->duree - $diff_duree;
					$line->debut = mbDate($sejour->_entree);
				}
			}
	  }
	  
	  // Ligne de sejour vers ligne de sortie
	  if($type == "sortie"){
	  	if((!$line->_fin_reelle && (mbDate($line->_debut_reel) < mbDate($sejour->_sortie))) || mbDate($line->_fin_reelle) <= mbDate($sejour->_sortie)){
	      continue;
	  	}
	  	// Modification des dates
	  	if($line->debut && $line->duree && ($line->debut < mbDate($sejour->_sortie))){
	      $diff_duree = mbDaysRelative($line->debut, mbDate($sejour->_sortie));
	      if($line->duree - $diff_duree > 0){
		      $line->duree = $line->duree - $diff_duree;
		      $line->debut = mbDate($sejour->_sortie);
	      }
			}
	  }
	  
		// On modifie la ligne en supprimant les validations
	  $line->_id = "";
	  $line->prescription_id = $prescription->_id;
	  $line->signee = 0;
	  $line->valide_infirmiere = 0;
	  $line->creator_id = $AppUI->user_id;
	  if($line->_class_name == "CPrescriptionLineMedicament"){
	    $line->valide_pharma = 0;
	  }
	  
	  // Dans le cas de la sortie, on supprime toutes les infos de durees et on ne stocke que la fin de la ligne
	  if($type == "sortie"){
	  	if($line->debut && $line->unite_duree && $line->duree){
	  		$line->fin = $line->_fin;
	  		$line->debut = "";
	  		$line->time_debut = "";
	  		$line->unite_duree = "";
	  		$line->duree = "";
	  	}
	  }
	  $msg = $line->store();
	  CAppUI::displayMsg($msg, "$line->_class_name-msg-create");
		
	  // Parcours des prises et creation des nouvelles prises
		foreach($line->_ref_prises as $prise){
			$prise->_id = "";
			$prise->object_id = $line->_id;
			$msg = $prise->store();
		  CAppUI::displayMsg($msg, "CPrisePosologie-msg-create");
		}
	}
}

$lite = CAppUI::pref('mode_readonly') ? 0 : 1;

echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription->_id, $prescription->object_id, null, null, null, null, null, true, $lite,'');</script>";
echo CAppUI::getMsg();
CApp::rip();
?>