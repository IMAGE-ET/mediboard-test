<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

$devenir_dentaire_id = CValue::get("devenir_dentaire_id");

$devenir_dentaire = new CDevenirDentaire();
$devenir_dentaire->load($devenir_dentaire_id);

$actes_dentaires = $devenir_dentaire->loadRefsActesDentaires();

foreach ($actes_dentaires as $_acte_dentaire) {
  $devenir_dentaire->_total_ICR += $_acte_dentaire->ICR;
}

$smarty = new CSmartyDP;

$smarty->assign("devenir_dentaire", $devenir_dentaire);

$smarty->display("inc_list_actes_dentaires.tpl");
