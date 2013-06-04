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

$devenir_dentaire_id = CValue::get("devenir_dentaire_id");
$patient_id = CValue::get("patient_id");

$devenir_dentaire = new CDevenirDentaire;

if ($devenir_dentaire_id) {
  $devenir_dentaire->load($devenir_dentaire_id);
  $etudiant = $devenir_dentaire->loadRefEtudiant();
  $etudiant->loadRefFunction();
  $actes_dentaires = $devenir_dentaire->loadRefsActesDentaires();
  
  foreach ($actes_dentaires as &$_acte_dentaire) {
    $devenir_dentaire->_total_ICR += $_acte_dentaire->ICR;
  }
}
else {
  $devenir_dentaire->patient_id = $patient_id;
}

$acte_dentaire = new CActeDentaire;
$acte_dentaire->rank = count($devenir_dentaire->_ref_actes_dentaires) + 1;

$smarty = new CSmartyDP;

$smarty->assign("devenir_dentaire", $devenir_dentaire);
$smarty->assign("acte_dentaire"   , $acte_dentaire);

$smarty->display("inc_form_devenir_dentaire.tpl");
