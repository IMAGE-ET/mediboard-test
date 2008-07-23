<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$line_id      = mbGetValueFromGet("line_id");
$object_class = mbGetValueFromGet("object_class");
$quantite     = mbGetValueFromGet("quantite");
$key_tab      = mbGetValueFromGet("key_tab");
$date         = mbGetValueFromGet("date");
$heure        = mbGetValueFromGet("heure");
$quantite = is_numeric($quantite) ? $quantite : '';
$prise_id = is_numeric($key_tab) ? $key_tab : '';
$unite_prise = !is_numeric($key_tab) ? utf8_decode($key_tab) : '';


// Si une prise est specifiée (pas de moment unitaire), on charge la prise pour stocker l'unite de prise
if($prise_id){
	$prise = new CPrisePosologie();
	$prise->load($prise_id);
	$unite_prise = $prise->unite_prise;
}

// Chargement de la ligne
$line = new $object_class;
$line->load($line_id);

if($line->_class_name == "CPrescriptionLineMedicament"){
  $line->_ref_produit->loadConditionnement();
}
$prise = new CPrisePosologie();
$prise->quantite = $quantite;

$dateTime = ($heure==24) ? "$date 23:59:00" : "$date $heure:00:00";

// Transmission
$transmission = new CTransmissionMedicale();



// Création du template
$smarty = new CSmartyDP();
$smarty->assign("transmission", $transmission);
$smarty->assign("line", $line);
$smarty->assign("unite_prise", $unite_prise);
$smarty->assign("prise", $prise);
$smarty->assign("sejour_id", $line->_ref_prescription->_ref_object->_id);
$smarty->assign("date", $date);
$smarty->assign("prise_id", $prise_id);
$smarty->assign("dateTime", $dateTime);
$smarty->display("../../dPprescription/templates/inc_vw_add_administration.tpl");

?>