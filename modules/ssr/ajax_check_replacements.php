<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$replacement = new CReplacement;
$replacements = $replacement->loadList();
foreach ($replacements as $_replacement) {
  $_replacement->loadRefConge();
  $_replacement->loadRefSejour();
  $_replacement->loadRefReplacer();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("replacements", $replacements);
$smarty->display("inc_check_replacements.tpl");

?>