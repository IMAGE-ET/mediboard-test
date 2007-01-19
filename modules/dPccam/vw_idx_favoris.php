<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$user = $AppUI->user_id;

$codesByChap = CFavoriCCAM::getOrdered($user);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("codesByChap", $codesByChap);

$smarty->display("vw_idx_favoris.tpl");

?>