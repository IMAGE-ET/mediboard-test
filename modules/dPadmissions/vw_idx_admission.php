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

// Initialisation de variables

$selAdmis = mbGetValueFromGetOrSession("selAdmis", "0");
$selSaisis = mbGetValueFromGetOrSession("selSaisis", "0");
$selTri = mbGetValueFromGetOrSession("selTri", "nom");
$date = mbGetValueFromGetOrSession("date", mbDate());

// Création du template
$smarty = new CSmartyDP(1);


$smarty->assign("date"     , $date);
$smarty->assign("selAdmis" , $selAdmis);
$smarty->assign("selSaisis", $selSaisis);
$smarty->assign("selTri"   , $selTri);

$smarty->display("vw_idx_admission.tpl");

?>