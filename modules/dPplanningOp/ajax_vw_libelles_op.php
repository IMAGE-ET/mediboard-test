<?php
/**
 * $Id: vw_libelles_op.php 5114 2014-01-02 08:45:23Z nicolasld $
 *
 * @package    Mediboard
 * @subpackage dPplanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision: 5114 $
 */

CCanDo::checkEdit();
$operation_id = CValue::getOrSession("operation_id");

$liaison = new CLiaisonLibelleInterv();
$liaison->operation_id = $operation_id;
/** @var CLiaisonLibelleInterv[] $liaisons */
$liaisons = $liaison->loadMatchingList("numero");

foreach ($liaisons as $_liaison) {
  $_liaison->loadRefLibelle();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("operation_id", $operation_id);
$smarty->assign("liaisons"    , $liaisons);
$smarty->display("inc_vw_libelles_op.tpl");