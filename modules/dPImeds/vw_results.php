<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g, $dPconfig;

// Il faut faire un test sur l'installation de sante400
$can->read &= (CModule::getInstalled("dPsante400") != null);
$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();
$id400 = new CIdSante400;
$id400->loadLatestFor($patient);
$patient400 = $id400->id400;

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
$id400 = new CIdSante400;
$id400->loadLatestFor($sejour);
$sejour400 = $id400->id400;

/**
 * Hack temporaire pour la démo, il faut gérer les id400 proprement, 
 * sur un tag configurable pour le module etc.
 **/
$patient400 = 00073648;
$sejour400 = 07500684;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"   , $patient);
$smarty->assign("patient400", $patient400);
$smarty->assign("sejour"    , $sejour);
$smarty->assign("sejour400" , $sejour400);
$smarty->assign("url"       , $dPconfig["dPImeds"]["url"]);

$smarty->display("vw_results.tpl");

?>