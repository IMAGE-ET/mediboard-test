<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$operation_id = mbGetValueFromGetOrSession("operation_id");
$date  = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;


$operation = new COperation();
if($operation_id){
  $operation->load($operation_id);
  $operation->loadRefs();
}

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null,$orderanesth);



// Création du template
$smarty = new CSmartyDP();


$smarty->assign("listAnesthType", $listAnesthType          );
$smarty->assign("listAnesths"   , $listAnesths             );
$smarty->assign("listChirs"     , $listChirs               );
$smarty->assign("selOp", $operation);
$smarty->assign("date"          , $date                    );
$smarty->assign("modif_operation", $modif_operation        );

$smarty->display("inc_vw_anesth.tpl");

?>