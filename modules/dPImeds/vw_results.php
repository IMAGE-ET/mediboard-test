<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision$
* @author Romain OLLIVIER
*/

global $can;

// Il faut faire un test sur l'installation de sante400
$can->read &= (CModule::getInstalled("dPsante400") != null);
$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadIPP();
$patient->loadRefsSejours();

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
if($sejour_id) {
  if(isset($patient->_ref_sejours[$sejour_id])) {
    $sejour =& $patient->_ref_sejours[$sejour_id];
  } else {
    mbSetValueToSession("sejour_id");
    $sejour = new CSejour;
  }
} else {
  $sejour = new CSejour;
}
$sejour->loadNumDossier();

// Valeurs de démonstration
//$patient->_IPP        = "00073648";
//$sejour->_num_dossier = "07500684";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("sejour" , $sejour);
$smarty->assign("idImeds", CImeds::getIdentifiants());
$smarty->assign("url"    , CImeds::getDossierUrl());

$smarty->display("vw_results.tpl");

?>