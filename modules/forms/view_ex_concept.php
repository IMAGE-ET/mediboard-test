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

$_GET["object_class"] = "CExConcept";

//$_GET["col"] = array("name");

CAppUI::requireModuleFile("system", "vw_object_tree_explorer");
