<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage forms
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$classes = array(
  "CExList",
  "CExConcept",
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("classes", $classes);
$smarty->display("view_import.tpl");
