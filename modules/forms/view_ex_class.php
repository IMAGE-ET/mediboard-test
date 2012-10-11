<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$_GET["object_class"] = "CExClass";
$_GET["tree_width"] = "15%";
$_GET["group_id"] = CGroups::loadCurrent()->_id;

echo <<<HTML
<div class='small-info'>Il est maintenant possible de lier un formulaire à plusieurs évènements 
depuis le volet <strong>Evènements déclencheurs</strong>, et de classer les formulaires par <strong>Tag</strong></div>
HTML;

CAppUI::requireModuleFile("system", "vw_object_tree_explorer");
