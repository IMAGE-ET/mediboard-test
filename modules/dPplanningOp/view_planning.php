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

CCanDo::checkRead();

$operation = new COperation();
$sejour    = new CSejour();

if ($sejour_id = CValue::get("sejour_id")) {
  $sejour->load($sejour_id);
  $sejour->loadNDA();
  $sejour->loadRefsFwd();
  $patient =& $sejour->_ref_patient;
  $patient->loadRefs();

  // Si le modèle est redéfini, on l'utilise
  $model = CCompteRendu::getSpecialModel($sejour->_ref_praticien, "COperation", "[FICHE DHE]");

  if ($model->_id) {
    CCompteRendu::streamDocForObject($model, $sejour);
  }
}

if ($operation_id = CValue::get("operation_id")) {
  $operation->load($operation_id);
  $operation->loadRefsFwd();
  $sejour = $operation->_ref_sejour;
  $operation->_ref_sejour->loadRefsFwd();
  $operation->_ref_sejour->loadNDA();
  $patient =& $operation->_ref_sejour->_ref_patient;
  $patient->loadRefs();

  // Si le modèle est redéfini, on l'utilise
  $model = CCompteRendu::getSpecialModel($sejour->_ref_praticien, "COperation", "[FICHE DHE]");

  if ($model->_id) {
    CCompteRendu::streamDocForObject($model, $operation);
  }
}

$today = CMbDT::date();

$group = CGroups::loadCurrent();
$group->loadConfigValues();
$simple_DHE = $group->_configs['dPplanningOp_COperation_DHE_mode_simple'];

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("operation" , $operation);
$smarty->assign("sejour"    , $sejour   );
$smarty->assign("today"     , $today    );
$smarty->assign("simple_DHE", $simple_DHE);

$smarty->display("view_planning.tpl");
