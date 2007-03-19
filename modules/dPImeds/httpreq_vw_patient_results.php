<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g, $dPconfig;

$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();
$id400 = new CIdSante400;
$id400->loadLatestFor($patient);
$patient400 = $id400->id400;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"   , $patient);
$smarty->assign("patient400", $patient400);
$smarty->assign("url"       , $dPconfig["dPImeds"]["url"]);

$smarty->display("inc_patient_results.tpl");