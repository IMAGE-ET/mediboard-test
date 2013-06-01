<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$operation_id = CValue::getOrSession("operation_id");
$operation = new COperation;
$operation->load($operation_id);
$operation->loadRefsActesCCAM();
foreach ($operation->_ref_actes_ccam as &$acte) {
  $acte->loadRefsFwd();
  $acte->guessAssociation();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("curr_op", $operation);

$smarty->display("inc_confirm_actes_ccam.tpl");
