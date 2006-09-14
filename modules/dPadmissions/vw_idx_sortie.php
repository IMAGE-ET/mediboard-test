<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Type d'affichage
$vue = mbGetValueFromGetOrSession("vue", 0);

// Récupération des dates
$date = mbGetValueFromGetOrSession("date", mbDate());

$now  = mbDate();

// Création du template
$smarty = new CSmartyDP(1);
$smarty->assign('date' , $date );
$smarty->assign('now' , $now );
$smarty->assign('vue' , $vue );

$smarty->display('vw_idx_sortie.tpl');

?>