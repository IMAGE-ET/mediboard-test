<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// Type d'affichage
$vue = mbGetValueFromGetOrSession("vue", 0);

// Récupération des dates
$date = mbGetValueFromGetOrSession("date", mbDate());

$date_actuelle = mbDateTime("00:00:00");
$date_demain = mbDateTime("00:00:00","+ 1 day");

$hier = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$now  = mbDate();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date_actuelle", $date_actuelle);
$smarty->assign("date_demain", $date_demain);
$smarty->assign("date" , $date );
$smarty->assign("now" , $now );
$smarty->assign("vue" , $vue );
$smarty->assign("hier", $hier);
$smarty->assign("demain", $demain);

$smarty->display("vw_idx_sortie.tpl");

?>