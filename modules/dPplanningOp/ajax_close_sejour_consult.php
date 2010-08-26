<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();

$now       = mbDateTime();
$yesterday = mbDateTime("-1 DAY");

$sejour = new CSejour();
$where['type']   = "= 'consult'";
$where['entree'] = "BETWEEN '$yesterday' AND '$now'";

// Cloture des sejours passés
$where['entree_reelle'] = "IS NOT NULL";
$where['sortie_reelle'] = "IS NULL";
$sejours = $sejour->loadList($where);

CAppUI::stepAjax(count($sejours)." séjours à clôturer", UI_MSG_OK);

foreach ($sejours as $_sejour) {
  $_sejour->sortie_reelle = $now;
	$msg = $_sejour->store();  
	CAppUI::stepAjax($msg ? "Séjour non clôturé" : "Séjour clôturé", $msg ? UI_MSG_WARNING : UI_MSG_OK);	
}

// Annulation des séjours sans entrée reelle ni sortie reelle
$where['entree_reelle'] = "IS NULL";
$where['sortie_reelle'] = "IS NULL";
$sejours = $sejour->loadList($where);

CAppUI::stepAjax(count($sejours)." séjours à annuler", UI_MSG_OK);

foreach ($sejours as $_sejour) {
  $_sejour->annule = 1;
  $msg = $_sejour->store();
  CAppUI::stepAjax($msg ? "Séjour non annulé" : "Séjour annulé", $msg ? UI_MSG_WARNING : UI_MSG_OK);  
}
