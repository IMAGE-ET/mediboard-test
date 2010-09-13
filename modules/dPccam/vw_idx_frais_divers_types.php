<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$frais_divers_type_id = CValue::getOrSession("frais_divers_type_id");

$type = new CFraisDiversType;
$type->load($frais_divers_type_id);

$list_types = $type->loadList(null, "code");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("type", $type);
$smarty->assign("list_types", $list_types);
$smarty->display("vw_idx_frais_divers_types.tpl");
