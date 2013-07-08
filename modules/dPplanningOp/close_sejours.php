<?php /** $Id $ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision: 6153 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();

$now                     = CMbDT::dateTime();
$number_of_previous_days = CValue::get("number_of_previous_days", 1);
$yesterday               = CMbDT::dateTime("-$number_of_previous_days DAY");
$types                   = CValue::get("types", "consult");
$limit                   = intval(CValue::get("limit", 1));
$cancel_sejours          = CValue::get("cancel_sejours", 1);

$types = explode("-", $types);
CMbArray::removeValue("", $types);

$sejour = new CSejour();
$where["type"]   = CSQLDataSource::prepareIn($types);
$where['entree'] = "BETWEEN '$yesterday' AND '$now'";
// Limite de 1
$limit = "0, $limit";

// Cloture des sejours passés
$where['entree_reelle'] = "IS NOT NULL";
$where['sortie_reelle'] = "IS NULL";
$order = "entree_reelle";
$sejours = $sejour->loadList($where, $order, $limit);
CAppUI::stepAjax(count($sejours)." séjours à clôturer", UI_MSG_OK);

foreach ($sejours as $_sejour) {
  $_sejour->sortie_reelle = $now;
  $msg = $_sejour->store();  
  CAppUI::stepAjax($msg ? "Séjour non clôturé" : "Séjour clôturé", $msg ? UI_MSG_WARNING : UI_MSG_OK);	
}

// Annulation des séjours sans entrée reelle ni sortie reelle
if ($cancel_sejours) {
  $where['entree_reelle'] = "IS NULL";
  $where['sortie_reelle'] = "IS NULL";
  $where['annule']        = "= '0'";
  $order = "entree_prevue";

  $sejours = $sejour->loadList($where, $order, $limit);

  CAppUI::stepAjax(count($sejours)." séjours à annuler", UI_MSG_OK);

  foreach ($sejours as $_sejour) {
    $_sejour->annule = 1;
    $msg = $_sejour->store();
    CAppUI::stepAjax($msg ? "Séjour non annulé" : "Séjour annulé", $msg ? UI_MSG_WARNING : UI_MSG_OK);
  }
}
