<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
if ($sejour_id) {
  $sejour->load($sejour_id);
  $sejour->canRead();
  $sejour->loadRefsFwd();
  $praticien =& $sejour->_ref_praticien;
  $patient =& $sejour->_ref_patient;
  $patient->loadRefsSejours();
  $sejours =& $patient->_ref_sejours;
  $sejour->loadRefsOperations();
  foreach ($sejour->_ref_operations as $_operation) {
    $_operation->loadRefsFwd();
    $_operation->_ref_chir->loadRefFunction();
    $_operation->loadBrancardage();
  }
  $sejour->loadRefsConsultAnesth();
  $sejour->_ref_consult_anesth->loadRefConsultation();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->display("inc_info_list_operations.tpl");
