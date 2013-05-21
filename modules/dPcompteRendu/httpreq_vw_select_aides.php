<?php

/**
 * Liste d'aides à la saisie pour une classe donnée
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */


$object_class   = CValue::get("object_class");
$field          = CValue::get("field");
$depend_value_1 = CValue::get("depend_value_1");
$depend_value_2 = CValue::get("depend_value_2");
$user_id        = CValue::get("user_id");
$no_enum        = CValue::get("no_enum");

// Chargement des aides
$object = new $object_class;
/** @var $object CMbObject */
$object->loadAides($user_id, null, $depend_value_1, $depend_value_2);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("object",  $object);
$smarty->assign("field",   $field);
$smarty->assign("no_enum", $no_enum);

$smarty->display("inc_vw_select_aides.tpl");
