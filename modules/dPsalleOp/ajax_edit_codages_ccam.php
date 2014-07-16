<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 23384 $
 */

$codage_id = CValue::get("codage_id");

$codage = new CCodageCCAM();
$codage->load($codage_id);
$codage->canDo();

if (!$codage->_can->edit) {
  CAppUI::redirect("m=system&a=access_denied");
}

$codage->loadPraticien()->loadRefFunction();
$codage->loadActesCCAM();
$codage->checkRules();
foreach ($codage->_ref_actes_ccam as $_acte) {
  $_acte->getTarif();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("codage", $codage);

$smarty->display("inc_edit_codages.tpl");