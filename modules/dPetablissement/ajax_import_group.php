<?php 

/**
 * $Id$
 *  
 * @category Forms
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$uid = preg_replace('/[^\d]/', '', CValue::get("uid"));

$temp = CAppUI::getTmpPath("group_import");
$file = "$temp/$uid";

$import = new CExClassImport($file);

/** @var DOMElement $group */
// Etablissements -------
$group = $import->getElementsbyClass("CGroups")->item(0);
$group_name = $import->getNamedValueFromElement($group, "text");

$data = array();

// Services -------
$data["CService"]        = $import->getObjectsList("CService", "nom");

// Functions -------
$data["CFunctions"]      = $import->getObjectsList("CFunctions", "text");

// Users -------
$data["CUser"]           = $import->getObjectsList("CUser", "user_username", false, false);

// Blocs -------
$data["CBlocOperatoire"] = $import->getObjectsList("CBlocOperatoire", "nom");

// Salles -------
$data["CSalle"]          = $import->getObjectsList("CSalle", "nom");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("group_name", $group_name);
$smarty->assign("uid", $uid);
$smarty->assign("data", $data);
$smarty->display("inc_import_group.tpl");