<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}

$field = mbGetValueFromGet("field", null);
$text  = mbGetValueFromGet("text" , null);
$class = mbGetValueFromGet("class", null);

// Liste des praticiens accessibles
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

// Nouvelle Aide à la saisie
$aide = new CAideSaisie();
$aide->class   = $class;
$aide->field   = $field;
$aide->text    = stripslashes($text);
$aide->user_id = $AppUI->user_id;

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("aide"     , $aide);
$smarty->assign("listFunc" , $listFunc);
$smarty->assign("listPrat" , $listPrat);

$smarty->display("vw_edit_aides.tpl");
?>