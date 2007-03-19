<?php /* $Id: edit_sorties.php 783 2006-09-14 12:44:01Z rhum1 $*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 783 $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g, $dPconfig;

$can->needsRead();

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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"    , $sejour);
$smarty->assign("sejour400" , $sejour400);
$smarty->assign("url"       , $dPconfig["dPImeds"]["url"]);

$smarty->display("inc_sejour_results.tpl");