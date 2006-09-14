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

$codeacte = mbGetValueFromGetOrSession("codeacte");
$code = new CCodeCCAM($codeacte);
$code->Load();

// Création du template
$smarty = new CSmartyDP(1);

// @todo : ne passer que $code. Adapter le template en conséquence
$smarty->assign("codeacte", strtoupper($code->code));
$smarty->assign("libelle" , $code->libelleLong);
$smarty->assign("rq"      , $code->remarques);
$smarty->assign("act"     , $code->activites);
$smarty->assign("codeproc", $code->procedure["code"]);
$smarty->assign("textproc", $code->procedure["texte"]);
$smarty->assign("place"   , $code->place);
$smarty->assign("chap"    , $code->chapitres);
$smarty->assign("asso"    , $code->assos);
$smarty->assign("incomp"  , $code->incomps);

$smarty->display("vw_full_code.tpl");

?>