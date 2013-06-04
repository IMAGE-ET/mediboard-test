<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// !! Attention, régression importante si ajout de type de paiement

// Récupération des paramètres
$operation_id = CValue::get("operation_id", null);
$op = new COperation;
$op->load($operation_id);
$op->loadRefsFwd();
$op->_ref_sejour->loadRefsFwd();
$patient =& $op->_ref_sejour->_ref_patient;

$pack_id = CValue::get("pack_id", null);

$pack = new CPack;
$pack->load($pack_id);

// Creation des template manager
$listCr = array();
foreach($pack->_modeles as $key => $value) {
  $listCr[$key] = new CTemplateManager;
  $listCr[$key]->valueMode = true;
  $op->fillTemplate($listCr[$key]);
  $patient->fillTemplate($listCr[$key]);
  $listCr[$key]->applyTemplate($value);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listCr", $listCr);

$smarty->display("print_pack.tpl");

?>