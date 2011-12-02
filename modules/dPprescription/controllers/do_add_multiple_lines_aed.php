<?php /* $Id:  $ */

/**
 *  @package Mediboard
 *  @subpackage dPprescription
 *  @version $Revision:  $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user = CUser::get();

$elements           = CValue::post("elements_id");
$codes_cip          = CValue::post("codes_cip");
$prescription_id    = CValue::post("prescription_id");
$debut              = CValue::post("debut");
$time_debut         = CValue::post("time_debut");
$jour_decalage      = CValue::post("jour_decalage");
$decalage_line      = CValue::post("decalage_line");
$mode_protocole     = CValue::post("mode_protocole","0");
$mode_pharma        = CValue::post("mode_pharma","0");
$praticien_id       = CValue::post("praticien_id", $user->_id);

$identifiants["elt"] = $elements;
$identifiants["med"] = $codes_cip;

$prescription = new CPrescription;
$prescription->load($prescription_id);
$is_praticien = CAppUI::$user->isPraticien();
$role_propre = CAppUI::conf("dPprescription CPrescription role_propre");

foreach ($identifiants as $type => $_identifiant_by_type){
	if (is_array($_identifiant_by_type)){
		foreach ($_identifiant_by_type as $_identifiant){
			if($type == "med"){
				$line = new CPrescriptionLineMedicament();
	      $line->code_cip = $_identifiant;
			} else {
				$line = new CPrescriptionLineElement();
				$line->element_prescription_id = $_identifiant;
			}		
			
			$line->prescription_id = $prescription_id;
		  $line->praticien_id = $praticien_id;
		  $line->creator_id = $user->_id;
			
			$line->debut = $debut;
	    $line->time_debut = $time_debut;
	    $line->jour_decalage = $jour_decalage;
	    $line->decalage_line = $decalage_line;
	    
			$msg = $line->store();
	    CAppUI::displayMsg($msg, "$line->_class-msg-create");
		}
  }
}

// Reload en full mode
if($mode_protocole || $mode_pharma){
  echo "<script type='text/javascript'>Prescription.reload('$prescription_id','','','$mode_protocole','$mode_pharma', null)</script>";
} else {
  echo "<script type='text/javascript'>Prescription.reloadPrescSejour('$prescription_id', null, null, null, null, null, null)</script>";
}
    
echo CAppUI::getMsg();
CApp::rip();
?>