<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage soins
* @version $Revision: $
* @author Alexis Granger
*/

global $can, $m, $AppUI;

$can->needsRead();

$praticien_id = mbGetValueFromGetOrSession("praticien_id" , $AppUI->user_id);
$signee       = mbGetValueFromGetOrSession("signee"       , 0       );  // par default les non signees
$date         = mbGetValueFromGetOrSession("date"         , mbDate());  // par default, date du jour
$type         = mbGetValueFromGetOrSession("type"         , "sejour");  // sejour - externe - sortie_manquante

// Calcul de date_max et date_min
$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:59", $date);

// Chargement de la liste des praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();

// Recherche des prescriptions
$where = array();
if($type == "sejour" || $type == "sortie_manquante"){
	$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id";
	$where["prescription.type"] = " = 'sejour'";
	$where[] = "(sejour.entree_prevue BETWEEN '$date_min' AND '$date_max') OR 
	 				  (sejour.sortie_prevue BETWEEN '$date_min' AND '$date_max') OR
				    (sejour.entree_prevue <= '$date_min' AND sejour.sortie_prevue >= '$date_max')";	
} else {
	$where["prescription.type"] = " = 'externe'";
  $ljoin["consultation"] = "prescription.object_id = consultation.consultation_id";
  $ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
  $where["plageconsult.date"] = " = '$date'";
}

if($signee == "0"){
$where[] = "(prescription_line_element.praticien_id = '$praticien_id' AND prescription_line_element.signee != '1') 
						OR (prescription_line_medicament.praticien_id = '$praticien_id' AND prescription_line_medicament.signee != '1')
            OR (perfusion.praticien_id = '$praticien_id' AND perfusion.signature_prat != '1')";
} else {
  $where[] = "(prescription_line_element.praticien_id = '$praticien_id') 
		       OR (prescription_line_medicament.praticien_id = '$praticien_id')
		       OR (perfusion.praticien_id = '$praticien_id')";
}

$ljoin["prescription_line_element"] = "prescription_line_element.prescription_id = prescription.prescription_id";
$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_id = prescription.prescription_id";
$ljoin["perfusion"] = "perfusion.prescription_id = prescription.prescription_id";

$prescriptions = array();
$prescription = new CPrescription();
$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);

if($type == "sortie_manquante"){
  foreach($prescriptions as $_prescription){
  	// Recherche d'une prescription de sortie correspondant à la prescription de sejour
    $_prescription_sortie = new CPrescription();
    $_prescription_sortie->type = "sortie";
    $_prescription_sortie->object_id = $_prescription->object_id;
    $_prescription_sortie->object_class = $_prescription->object_class;
    $_prescription_sortie->loadMatchingObject();
    if($_prescription_sortie->_id){
    	unset($prescriptions[$_prescription->_id]);
    }
  }
}

$plageconsult = new CPlageconsult();
$plageconsult->date = $date;

if(!$praticien_id){
	$user_courant = new CMediusers();
	$user_courant->load($AppUI->user_id);
	if($user_courant->isPraticien()){
		$praticien_id = $user_courant->_id;
	}
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("type", $type);
$smarty->assign("date", $date);
$smarty->assign("signee", $signee);
$smarty->assign("prescriptions", $prescriptions);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("plageconsult", $plageconsult);
$smarty->display('vw_bilan_prescription.tpl');

?>