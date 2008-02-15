<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2165 $
* @author Sherpa
*/

global $can;
$can->needsEdit();

mbDump($_GET, "Paramètres à analyser");

$numdos = mbGetValueFromGet("numdos");
$sejour = CSpObjectHandler::getMbObjectFor("CSejour", $numdos);
$sejour->loadRefPatient();

if (!$sejour->_id) {
  trigger_error("Séjour avec le numéro de dossier '$numdos' introuvable dans l'établissement", E_USER_WARNING);
  return;
}

foreach ($_GET["actes"] as $idinterv => $actes) {
  $operation = CSpObjectHandler::getMbObjectFor("COperation", $idinterv);

  if (!$operation->_id) {
	  trigger_error("Operation avec l'identifiant '$idinterv' introuvable dans l'établissement", E_USER_WARNING);
	  continue;
	}

	if ($operation->sejour_id != $sejour->_id) {
	  trigger_error("Operation avec l'identifiant '$idinterv' non associé au séjour numéro '$numdos'", E_USER_WARNING);
	  continue;
	}
	
	$sejour->_ref_operations[$operation->_id] =& $operation;
	
	// Ajout des actes à importer
  foreach ($actes as $tokenCCAM) {
//    $spDetCCAM = CSpDetCCAM::fromToken($token);
//    $acteCCAM = $spDetCCAM->mapTo();
  }
  
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->display("import_actes.tpl");
?>