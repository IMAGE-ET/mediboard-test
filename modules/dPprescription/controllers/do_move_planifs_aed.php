<?php /*  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_id = CValue::post("object_id");
$object_class = CValue::post("object_class");
$prise_id = CValue::post("prise_id");
$datetime = CValue::post("datetime");
$nb_hours = CValue::post("nb_hours");
$quantite = CValue::post("quantite");

$limite_datetime = mbDateTime("$nb_hours HOURS", $datetime);

$lines = array();

$object = new $object_class;
$object->load($object_id);

if($object instanceof CPrescriptionLineMix){
	$prescription_line_mix = new CPrescriptionLineMix();
	$prescription_line_mix->load($object_id);
	$prescription_line_mix->loadRefsLines();
	$lines = $prescription_line_mix->_ref_lines;
} else {
	$lines[] = $object;
}

foreach($lines as $_line){
	$object_id = $_line->_id;
	$object_class = $_line->_class;
	
  $all_planifs = array();
	
	// Chargement des planifs systemes concernées par cette replanification
	$planif = new CPlanificationSysteme();
	$where = array();
	$where["object_id"] = " = '$object_id'";
	$where["object_class"] = " = '$object_class'";
	$where[] = "DATE_FORMAT(dateTime,'%Y-%m-%d %H:00:00') > '$datetime'";
	if($prise_id){
    $where["prise_id"] = " = '$prise_id'";		
	}
	$planifs = $planif->loadList($where);

	foreach($planifs as $_planif){
		$original_datetime = mbTransformTime(null, $_planif->dateTime, "%Y-%m-%d %H:00:00");
		
	  // Chargement d'une eventuelle replanification manuelle de la planif systeme
		$manual_planif = new CAdministration();
		$manual_planif->object_id = $object_id;
		$manual_planif->object_class = $object_class;
	  $manual_planif->original_dateTime = $original_datetime;
		if($_planif->prise_id){
		  $manual_planif->prise_id = $_planif->prise_id;
    }
	  $manual_planif->loadMatchingObject();
		
		// Si la planif n'a pas ete replanifiée, on l'ajoute dans le tableau de planif
		if(!$manual_planif->_id){
		  $all_planifs[$_planif->_id] = $manual_planif;
    }
	}
	
	$administration = new CAdministration();
	$where = array();
  $where["planification"] = " = '1'";
	$where["object_id"] = " = '$object_id'";
	$where["object_class"] = " = '$object_class'";
  if($prise_id){
    $where["prise_id"] = " = '$prise_id'";
  }
	
	$limite_datetime = mbTransformTime(null, $limite_datetime, "%Y-%m-%d %H:00:00");
	
	$where[] = "DATE_FORMAT(dateTime,'%Y-%m-%d %H:00:00') > '$limite_datetime'";
	$adms = $administration->loadList($where);
	
	
	foreach($adms as $_adm){
		$all_planifs[$_adm->_id] = $_adm;
	}
	
	foreach($all_planifs as $_planif_sys_id => $manual_planif) {
    
		if($manual_planif->_id){
			$manual_planif->dateTime = mbDateTime("$nb_hours HOURS", $manual_planif->dateTime);
			$manual_planif->store();
		} else {
			$_planif = $planifs[$_planif_sys_id];
      $original_datetime = mbTransformTime(null, $_planif->dateTime, "%Y-%m-%d %H:00:00");
    
			if($object_class == "CPrescriptionLineMixItem"){
				$_line->updateQuantiteAdministration();
        $_planif->unite_prise = $_line->_unite_administration;
        $quantite = $_line->_quantite_administration;
			}
			
		  $planification = new CAdministration();
		  $planification->object_id = $object_id;
		  $planification->object_class = $object_class;
		  $planification->planification = 1;
		  $planification->unite_prise = $_planif->unite_prise;
			if($_planif->prise_id){
			  $planification->prise_id = $_planif->prise_id;
      }
		  $planification->quantite = $quantite;
		  $planification->administrateur_id = CAppUI::$user->_id;
      $planification->original_dateTime = $original_datetime;
			$planification->dateTime = mbDateTime("$nb_hours HOURS", $original_datetime);
			
		  $planification->store();
		}
	}
}

CApp::rip();

?>