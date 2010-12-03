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
  $_replacement->loadDates();
	$_replacement->_ref_conge->loadRefUser();
	$_replacement->_ref_sejour->loadRefPatient();
	$count = $_replacement->checkCongesRemplacer();
  if (!$count) {
  	unset($replacements[$_replacement->_id]);
		continue;
  }
	
  $replacer = $_replacement->loadRefReplacer();
	$replacer->loadRefFunction();
	
  $_replacement->makeFragments();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("replacements", $replacements);
$smarty->display("inc_check_replacements.tpl");

?>