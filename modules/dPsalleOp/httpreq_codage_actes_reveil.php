<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;

$operation_id = mbGetValueFromGetOrSession("operation_id");

$date  = mbGetValueFromGetOrSession("date", mbDate());
$date_now = mbDate();
$modif_operation = $date>=$date_now;

$operation = new COperation();
$operation->load($operation_id);


//Chargement de la liste des praticiens
$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens();

// Chargement de la liste des anesthesistes
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$operation->updateFormFields();
$operation->loadRefsActesCCAM();
$operation->loadExtCodesCCAM();
$operation->getAssociationCodesActes();
$operation->loadPossibleActes();
$operation->loadRefPraticien();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("modif_operation"  , $modif_operation);
$smarty->assign("listAnesths"      , $listAnesths );
$smarty->assign("listChirs"        , $listChirs   );
$smarty->assign("operation"        , $operation   );
$smarty->display("httpreq_codage_actes_reveil.tpl");

?>