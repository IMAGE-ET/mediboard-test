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

$now  = mbDate();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date" , $date );
$smarty->assign("now" , $now );
$smarty->assign("vue" , $vue );

$smarty->display("vw_idx_sortie.tpl");

?>