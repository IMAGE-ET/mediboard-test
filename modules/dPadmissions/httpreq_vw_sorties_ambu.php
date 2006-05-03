<?php /* $Id: httpreq_vw_sorties_ambu.php,v 1.1 2006/04/24 07:57:46 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision: 1.1 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPhospi", "affectation"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Type d'affichage
$vue = mbGetValueFromGetOrSession("vue", 0);

// Récupération des dates
$date = mbGetValueFromGetOrSession("date", mbDate());

$now  = mbDate();

// Récupération des sorties du jour
$list = new CAffectation;
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";
$ljoin["operations"] = "operations.operation_id = affectation.operation_id";
$ljoin["lit"] = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"] = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"] = "service.service_id = chambre.service_id";
$ljoin["patients"] = "operations.pat_id = patients.patient_id";
$where["sortie"] = "BETWEEN '$limit1' AND '$limit2'";
if($vue) {
  $where["effectue"] = "= 0";
}
$order = "patients.nom, patients.prenom";
$where["type_adm"] = "= 'ambu'";
$listAmbu = $list->loadList($where, $order, null, null, $ljoin);
foreach($listAmbu as $key => $value) {
  $listAmbu[$key]->loadRefsFwd();
  if($listAmbu[$key]->_ref_next->affectation_id) {
    unset($listAmbu[$key]);
  } else {
    $listAmbu[$key]->_ref_operation->loadRefsFwd();
    $listAmbu[$key]->_ref_operation->_ref_chir->loadRefsFwd();
    $listAmbu[$key]->_ref_lit->loadRefsFwd();
    $listAmbu[$key]->_ref_lit->_ref_chambre->loadRefsFwd();
  }
}

// Création du template
require_once($AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;
$smarty->assign('date' , $date );
$smarty->assign('now' , $now );
$smarty->assign('vue' , $vue );
$smarty->assign('listAmbu' , $listAmbu );

$smarty->display('inc_vw_sorties_ambu.tpl');

?>