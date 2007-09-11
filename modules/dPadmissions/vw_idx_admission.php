<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// Initialisation de variables

$selAdmis = mbGetValueFromGetOrSession("selAdmis", "0");
$selSaisis = mbGetValueFromGetOrSession("selSaisis", "0");
$selTri = mbGetValueFromGetOrSession("selTri", "nom");
$date = mbGetValueFromGetOrSession("date", mbDate());

$date_actuelle = mbDateTime("00:00:00");
$date_demain = mbDateTime("00:00:00","+ 1 day");

$hier = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

// Création du template
$smarty = new CSmartyDP();


$smarty->assign("date_demain", $date_demain);
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date"     , $date);
$smarty->assign("selAdmis" , $selAdmis);
$smarty->assign("selSaisis", $selSaisis);
$smarty->assign("selTri"   , $selTri);
$smarty->assign("hier", $hier);
$smarty->assign("demain", $demain);

$smarty->display("vw_idx_admission.tpl");

?>