<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsaleOp
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $a, $can, $m, $g;


$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$salle_id = mbGetValueFromGetOrSession("salle_id");

$tabOp = array();

$actes_ccam = array();
// Creation de la liste des praticiens
$praticiens = array();
// Tableau qui stocke le nombre d'acte non signé par praticien
$nonSigne = array();

$date = mbGetValueFromGetOrSession("date", mbDate());
$dialog = mbGetValueFromGet("dialog");

// Si mode dialog, on efface les variables de tri
if($dialog){
  $praticien_id = "";
  $salle_id = "";
}

$object_id = mbGetValueFromGet("object_id");
$object_class = mbGetValueFromGet("object_class");

$salle = new CSalle();
$salle->group_id = $g;
$salles = $salle->loadMatchingList();

// Signature des actes en definissant un objet
if($dialog){
  // Recuperation de l'operation
	// Chargement de l'objet
	$object = new $object_class;
	$object->load($object_id);
	$operations[] = $object;
} else {
 // Signature des actes en definissant une date
	$operation = new COperation();
	$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
	$where[] = "plagesop.date = '$date'";
	
	// Tri par salle
	if($salle_id){
	  $where["plagesop.salle_id"] = " = '$salle_id'";
	}
	$operations = $operation->loadList($where, null, null, null, $ljoin);
}


// Parcours du tableau d'operations, et stockage dans un tableau de salle
foreach($operations as $key => $op){
  // Chargement de la plage op de l'operation
  $op->loadRefPlageOp();
  $op->loadView();
  // Chargement des actes CCAM de l'operation
  $op->loadRefsActesCCAM();
  // Tableau de stockage des operations
  $tabOp[$op->_id] = $op;
	// Classement des actes par executant
	foreach($op->_ref_actes_ccam as $key => $acte_ccam){
	  // Mise a jour de la liste des praticiens
	  if(!array_key_exists($acte_ccam->executant_id, $praticiens)){
	    $praticien = new CMediusers();
	    $praticien->load($acte_ccam->executant_id);
	    $praticien->loadRefFunction();
	    $praticiens[$acte_ccam->executant_id] = $praticien;
	    // initialisation du tableau d'actes non signés
	    $nonSigne[$acte_ccam->executant_id] = 0;
	  }  
	  // Chargement de l'executant de l'acte CCAM
	  $acte_ccam->loadRefExecutant();
	  // Chargement du tarif
	  $acte_ccam->getTarif();
	  
	  // Si tri par praticien
	  if($praticien_id){
      if($acte_ccam->executant_id == $praticien_id){
	      $actes_ccam[$op->_ref_salle->nom][$op->_id][$acte_ccam->executant_id][] = $acte_ccam;
	    }
	  } else {
	      $actes_ccam[$op->_ref_salle->nom][$op->_id][$acte_ccam->executant_id][] = $acte_ccam;
	  }
	  //$actes_ccam[$acte_ccam->executant_id][] = $acte_ccam;
	  if(!$acte_ccam->signe){
	    @$nonSigne[$op->_id][$acte_ccam->executant_id]++;
	  }
	}
}

// Tri par salle
ksort($actes_ccam);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tabOp", $tabOp);
$smarty->assign("date", $date);
$smarty->assign("nonSigne", $nonSigne);
$smarty->assign("redirectUrl", $a);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("dialog", $dialog);
$smarty->assign("salles", $salles);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("salle_id", $salle_id);

if($dialog){
  $smarty->assign("object", $object);
}
$smarty->assign("actes_ccam", $actes_ccam);

$smarty->display("vw_signature_actes.tpl");
?>