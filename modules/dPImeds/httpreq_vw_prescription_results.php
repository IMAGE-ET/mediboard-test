<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $can;
$can->needsRead();

$prescription_id = mbGetValueFromGetOrSession("prescription_id");
$prescription = new CPrescriptionLabo();
$prescription->load($prescription_id);
$idPresc = $prescription->getIdExterne();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("prescription" , $prescription);
$smarty->assign("numPresc", $idPresc->id400);
$smarty->assign("idImeds", CImeds::getIdentifiants());
$smarty->assign("url"    , CImeds::getDossierUrl());

$smarty->display("inc_prescription_results.tpl");

?>