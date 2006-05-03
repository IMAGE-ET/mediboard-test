<?php /* $Id: vw_idx_chapter.php,v 1.9 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision: 1.9 $
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once($AppUI->getModuleClass("dPcim10", "codecim10"));

$lang = mbGetValueFromGetOrSession("lang", LANG_FR);

$cim10 = new CCodeCIM10();
$chapter = $cim10->getSommaire($lang);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('lang', $lang);
$smarty->assign('chapter', $chapter);

$smarty->display('vw_idx_chapter.tpl');

?>