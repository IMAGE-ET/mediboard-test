<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$patient_id = CValue::get("patient_id");
$devenir_dentaire_id = CValue::get("devenir_dentaire_id");

$devenir_dentaire = new CDevenirDentaire;
$devenir_dentaire->patient_id = $patient_id;

$devenirs_dentaires = $devenir_dentaire->loadMatchingList();

foreach ($devenirs_dentaires as &$_devenir_dentaire) {
  $_etudiant = $_devenir_dentaire->loadRefEtudiant();
  $_etudiant->loadRefFunction();
  $_devenir_dentaire->countRefsActesDentaires();
}

$smarty = new CSmartyDP;

$smarty->assign("devenirs_dentaires", $devenirs_dentaires);
$smarty->assign("devenir_dentaire_id", $devenir_dentaire_id);

$smarty->display("inc_list_devenirs_dentaires.tpl");

?>