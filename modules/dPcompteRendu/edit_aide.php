<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
$field        = mbGetValueFromGet("field");
$text         = utf8_decode(mbGetValueFromGet("text" ));
$class        = mbGetValueFromGet("class");
$depend_value_1 = mbGetValueFromGet("depend_value_1");
$depend_value_2 = mbGetValueFromGet("depend_value_2");

// Liste des users accessibles
$listPrat = new CMediusers();
$listFct = $listPrat->loadFonctions(PERM_EDIT);
$listPrat = $listPrat->loadUsers(PERM_EDIT);

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

// Objet ciblé
$object = new $class;
$dependValues = null;

if (count($helped = $object->_specs[$field]->helped)) {
  foreach ($helped as $i => $depend_field) {
    $key = "depend_value_" . ($i+1);
    $dependValues[$key] = @$object->_specs[$depend_field]->_locales;
  }
}

// Nouvelle Aide à la saisie
$aide = new CAideSaisie();
$aide->class        = $class;
$aide->field        = $field;
$aide->text         = stripslashes($text);
$aide->depend_value_1 = $depend_value_1;
$aide->depend_value_2 = $depend_value_2;
$aide->user_id = $AppUI->user_id;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("aide"     , $aide);
$smarty->assign("dependValues", $dependValues);
$smarty->assign("listFunc" , $listFunc);
$smarty->assign("listPrat" , $listPrat);

$smarty->display("vw_edit_aides.tpl");
?>