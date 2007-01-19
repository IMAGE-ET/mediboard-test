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

// Liste des users accessibles
$listPrat = new CMediusers();
$listFct = $listPrat->loadFonctions(PERM_EDIT);
$where = array();
$where["users_mediboard.function_id"] = db_prepare_in(array_keys($listFct));
$ljoin = array();
$ljoin["users"] = "`users`.`user_id` = `users_mediboard`.`user_id`";
$order = "`users`.`user_last_name`, `users`.`user_first_name`";
$listPrat = $listPrat->loadList($where, $order, null, null, $ljoin);
foreach ($listPrat as $keyUser => $mediuser) {
  $mediuser->_ref_function =& $listFct[$mediuser->function_id];
}

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

// Nouvelle Aide à la saisie
$aide = new CAideSaisie();
$aide->class   = $class;
$aide->field   = $field;
$aide->text    = stripslashes($text);
$aide->user_id = $AppUI->user_id;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("aide"     , $aide);
$smarty->assign("listFunc" , $listFunc);
$smarty->assign("listPrat" , $listPrat);

$smarty->display("vw_edit_aides.tpl");
?>