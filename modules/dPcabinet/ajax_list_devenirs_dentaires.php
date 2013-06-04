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

$patient_id = CValue::get("patient_id");
$devenir_dentaire_id = CValue::get("devenir_dentaire_id");

$devenir_dentaire = new CDevenirDentaire;
$devenir_dentaire->patient_id = $patient_id;

/** @var CDevenirDentaire[] $devenirs_dentaires */
$devenirs_dentaires = $devenir_dentaire->loadMatchingList();

foreach ($devenirs_dentaires as $_devenir) {
  $_etudiant = $_devenir->loadRefEtudiant();
  $_etudiant->loadRefFunction();
  $_devenir->countRefsActesDentaires();
}

$smarty = new CSmartyDP;

$smarty->assign("devenirs_dentaires", $devenirs_dentaires);
$smarty->assign("devenir_dentaire_id", $devenir_dentaire_id);

$smarty->display("inc_list_devenirs_dentaires.tpl");
