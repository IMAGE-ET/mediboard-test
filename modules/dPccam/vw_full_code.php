<?php /* $Id: vw_full_code.php,v 1.17 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision: 1.17 $
* @author Romain Ollivier
*/

GLOBAL $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPccam', 'acte') );
$codeacte = mbGetValueFromGetOrSession("codeacte");
$code = new CCodeCCAM($codeacte);
$code->Load();

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

// @todo : ne passer que $code. Adapter le template en conséquence
$smarty->assign('codeacte', strtoupper($code->code));
$smarty->assign('libelle', $code->libelleLong);
$smarty->assign('rq', $code->remarques);
$smarty->assign('act', $code->activites);
$smarty->assign('codeproc', $code->procedure["code"]);
$smarty->assign('textproc', $code->procedure["texte"]);
$smarty->assign('place', $code->place);
$smarty->assign('chap', $code->chapitres);
$smarty->assign('asso', $code->assos);
$smarty->assign('incomp', $code->incomps);

$smarty->display('vw_full_code.tpl');

?>