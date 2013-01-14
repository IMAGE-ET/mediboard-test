<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage forms
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$_GET["object_class"] = "CExClass";
$_GET["tree_width"] = "12%";
$_GET["group_id"] = CGroups::loadCurrent()->_id;

CAppUI::requireModuleFile("system", "vw_object_tree_explorer");
