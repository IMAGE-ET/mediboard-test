<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPhospi", "affectation"));

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Type d'affichage
$vue = mbGetValueFromGetOrSession("vue", 0);

// Récupération des dates
$date = mbGetValueFromGetOrSession("date", mbDate());

$now  = mbDate();

// Création du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;
$smarty->assign('date' , $date );
$smarty->assign('now' , $now );
$smarty->assign('vue' , $vue );

$smarty->display('vw_idx_sortie.tpl');

?>