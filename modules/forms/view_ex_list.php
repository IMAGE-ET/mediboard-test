<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$_GET["object_class"] = "CExList";

//$_GET["col"] = array("name");

CAppUI::requireModuleFile("system", "vw_object_tree_explorer");
