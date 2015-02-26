<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage mvsante
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision:$
 */

CCanDo::checkEdit();
$commande_id = CValue::get("commande_id");

$commande = new CCommandeMaterielOp();
$commande->load($commande_id);

$commande->loadView();
$op = $commande->_ref_operation;
$op->loadRefChir()->loadRefFunction();
$op->loadRefPlageOp();

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("commande", $commande);

$smarty->display("vw_edit_commande.tpl");
