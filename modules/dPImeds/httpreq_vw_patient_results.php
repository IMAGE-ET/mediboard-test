<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

global $can;
$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadIPP();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("idImeds", CImeds::getIdentifiants());
$smarty->assign("url"    , CImeds::getDossierUrl());

$smarty->display("inc_patient_results.tpl");

?>