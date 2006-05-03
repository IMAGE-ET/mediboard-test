<?php /* $Id: edit_sorties.php,v 1.12 2006/04/21 16:56:38 mytto Exp $*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1.12 $
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPhospi", "affectation"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Type d'affichage
$vue = mbGetValueFromGetOrSession("vue", 0);
$typeOrder = mbGetValueFromGetOrSession("typeOrder", 1);

// Récupération de la journée à afficher
$year  = mbGetValueFromGetOrSession("year" , date("Y"));
$month = mbGetValueFromGetOrSession("month", date("m")-1) + 1;
$day   = mbGetValueFromGetOrSession("day"  , date("d"));

$date = mbGetValueFromGetOrSession("date", mbDate());

$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";
$ljoin["operations"] = "operations.operation_id = affectation.operation_id";
$ljoin["patients"] = "operations.pat_id = patients.patient_id";
$ljoin["lit"] = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"] = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"] = "service.service_id = chambre.service_id";
$where["sortie"] = "BETWEEN '$limit1' AND '$limit2'";
$where["type_adm"] = "<> 'exte'";
if($vue) {
  $where["confirme"] = "= 0";
}
if($typeOrder)
  $order = "service.nom, chambre.nom, lit.nom";
else
  $order = "patients.nom, patients.prenom";

// Récupération des déplacements du jour
$deplacements = new CAffectation;
$deplacements = $deplacements->loadList($where, $order, null, null, $ljoin);
foreach($deplacements as $key => $value) {
  $deplacements[$key]->loadRefsFwd();
  if(!$deplacements[$key]->_ref_next->affectation_id) {
    unset($deplacements[$key]);
  } else {
    $deplacements[$key]->_ref_operation->loadRefsFwd();
    $deplacements[$key]->_ref_operation->_ref_chir->loadRefsFwd();
    $deplacements[$key]->_ref_lit->loadRefsFwd();
    $deplacements[$key]->_ref_lit->_ref_chambre->loadRefsFwd();
    $deplacements[$key]->_ref_next->loadRefsFwd();
    $deplacements[$key]->_ref_next->_ref_lit->loadRefsFwd();
    $deplacements[$key]->_ref_next->_ref_lit->_ref_chambre->loadRefsFwd();
  }
}

// Récupération des sorties ambu du jour
$where["type_adm"] = "= 'ambu'";
$sortiesAmbu = new CAffectation;
$sortiesAmbu = $sortiesAmbu->loadList($where, $order, null, null, $ljoin);
foreach($sortiesAmbu as $key => $value) {
  $sortiesAmbu[$key]->loadRefsFwd();
  if($sortiesAmbu[$key]->_ref_next->affectation_id) {
    unset($sortiesAmbu[$key]);
  } else {
    $sortiesAmbu[$key]->_ref_operation->loadRefsFwd();
    $sortiesAmbu[$key]->_ref_operation->_ref_chir->loadRefsFwd();
    $sortiesAmbu[$key]->_ref_lit->loadRefsFwd();
    $sortiesAmbu[$key]->_ref_lit->_ref_chambre->loadRefsFwd();
  }
}

// Récupération des sorties hospi complete du jour
$where["type_adm"] = "= 'comp'";
$sortiesComp = new CAffectation;
$sortiesComp = $sortiesComp->loadList($where, $order, null, null, $ljoin);
foreach($sortiesComp as $key => $value) {
  $sortiesComp[$key]->loadRefsFwd();
  if($sortiesComp[$key]->_ref_next->affectation_id) {
    unset($sortiesComp[$key]);
  } else {
    $sortiesComp[$key]->_ref_operation->loadRefsFwd();
    $sortiesComp[$key]->_ref_operation->_ref_chir->loadRefsFwd();
    $sortiesComp[$key]->_ref_lit->loadRefsFwd();
    $sortiesComp[$key]->_ref_lit->_ref_chambre->loadRefsFwd();
  }
}

// Création du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;
$smarty->assign('date' , $date );
$smarty->assign('deplacements' , $deplacements );
$smarty->assign('sortiesAmbu'  , $sortiesAmbu  );
$smarty->assign('sortiesComp'  , $sortiesComp  );
$smarty->assign('vue'          , $vue          );

$smarty->display('edit_sorties.tpl');

?>