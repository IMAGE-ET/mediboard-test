<?php /* $Id: vw_time_op.php,v 1.4 2006/04/28 13:27:10 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1.4 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$codeCCAM = strtoupper(mbGetValueFromGetOrSession("codeCCAM", ""));
$prat_id  = mbGetValueFromGetOrSession("prat_id", 0);
$intervalle = mbGetValueFromGetOrSession("intervalle", 2);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$sql = "SELECT" .
       "\nusers.user_last_name, users.user_first_name," .
       "\nCOUNT(operations.operation_id) AS total," .
       "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.sortie_bloc)-TIME_TO_SEC(operations.entree_bloc))) as duree_bloc," .
       "\nSEC_TO_TIME(STD(TIME_TO_SEC(operations.sortie_bloc)-TIME_TO_SEC(operations.entree_bloc))) as ecart_bloc," .
       "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as duree_operation," .
       "\nSEC_TO_TIME(STD(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as ecart_operation," .
       "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.temp_operation))) AS estimation,";
if($codeCCAM)
  $sql .= "\n'$codeCCAM' AS ccam";
else
  $sql .= "\noperations.codes_ccam AS ccam";
$sql .="\nFROM operations" .
       "\nLEFT JOIN users" .
       "\nON operations.chir_id = users.user_id" .
       "\nLEFT JOIN plagesop" .
       "\nON operations.plageop_id = plagesop.id" .
       "\nWHERE operations.entree_bloc IS NOT NULL" .
       "\nAND operations.debut_op IS NOT NULL" .
       "\nAND operations.fin_op IS NOT NULL" .
       "\nAND operations.sortie_bloc IS NOT NULL" .
       "\nAND operations.entree_bloc < operations.debut_op";
       "\nAND operations.debut_op < operations.fin_op";
       "\nAND operations.fin_op < operations.sortie_bloc";
switch($intervalle) {
  case 0:
    $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-1 month")."' AND '".mbDate()."'";
    break;
  case 1:
    $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-6 month")."' AND '".mbDate()."'";
    break;
  case 2:
    $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-1 year")."' AND '".mbDate()."'";
    break;
}
if($prat_id)
  $sql .= "\nAND operations.chir_id = '$prat_id'";
if($codeCCAM)
  $sql .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
$sql .= "\nGROUP BY operations.chir_id, ccam" .
        "\nORDER BY users.user_last_name, users.user_first_name, ccam";

$listOps = db_loadList($sql);

$sql = "SELECT" .
       "\n'1' AS groupall," .
       "\nCOUNT(operations.operation_id) AS total," .
       "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.sortie_bloc)-TIME_TO_SEC(operations.entree_bloc))) as duree_bloc," .
       "\nSEC_TO_TIME(STD(TIME_TO_SEC(operations.sortie_bloc)-TIME_TO_SEC(operations.entree_bloc))) as ecart_bloc," .
       "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as duree_operation," .
       "\nSEC_TO_TIME(STD(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as ecart_operation," .
       "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.temp_operation))) AS estimation" .
       "\nFROM operations" .
       "\nLEFT JOIN plagesop" .
       "\nON operations.plageop_id = plagesop.id" .
       "\nWHERE operations.entree_bloc IS NOT NULL" .
       "\nAND operations.debut_op IS NOT NULL" .
       "\nAND operations.fin_op IS NOT NULL" .
       "\nAND operations.sortie_bloc IS NOT NULL" .
       "\nAND operations.entree_bloc < operations.debut_op";
       "\nAND operations.debut_op < operations.fin_op";
       "\nAND operations.fin_op < operations.sortie_bloc";
switch($intervalle) {
  case 0:
    $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-1 month")."' AND '".mbDate()."'";
    break;
  case 1:
    $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-6 month")."' AND '".mbDate()."'";
    break;
  case 2:
    $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-1 year")."' AND '".mbDate()."'";
    break;
}
if($prat_id)
  $sql .= "\nAND operations.chir_id = '$prat_id'";
if($codeCCAM)
  $sql .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";
$sql .= "\nGROUP BY groupall";

db_loadHash($sql, $total);

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('prat_id'   , $prat_id);
$smarty->assign('codeCCAM'  , $codeCCAM);
$smarty->assign('intervalle'  , $intervalle);
$smarty->assign('listPrats' , $listPrats);
$smarty->assign('listOps'   , $listOps);
$smarty->assign('total'     , $total);

$smarty->display('vw_time_op.tpl');

?>