<?php /* $Id: print_noncote.php  $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 15681 $
* @author 
*/

// Récupération des paramètres
$filter = new CPlageconsult();

$filter->_date_min = CValue::getOrSession("_date_min", CMbDT::date());
$filter->_date_max = CValue::getOrSession("_date_max", CMbDT::date());
$filter->_type_affichage  = CValue::getOrSession("_type_affichage" , 1);

// Tri sur les praticiens
$mediuser = CMediusers::get();
$mediuser->loadRefFunction();

$prat = new CMediusers;
$prat->load(CValue::getOrSession("chir"));
$prat->loadRefFunction();
if ($prat->_id) {
  $listPrat = array($prat->_id => $prat);
}
else {
  $listPrat = $mediuser->loadPraticiensCompta();
}

// On recherche toutes les consultations non cotés
$ljoin = array();
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";

$where = array();
$where["tarif"] = " IS NULL ";
$where["codes_ccam"] = " IS NULL";
$where["secteur1"] = " = 0";
$where["secteur2"] = " = 0";
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
$where[]= "plageconsult.date >= '$filter->_date_min' AND plageconsult.date <= '$filter->_date_max'";

$order[] = "plageconsult.date, plageconsult.chir_id";

$consultation = new CConsultation();
$listConsults = $consultation->loadList($where, $order, null, null, $ljoin);

$listConsults_date = array();

foreach($listConsults as $consult){
	$consult->loadRefPatient();
	$listConsults_date[$consult->_ref_plageconsult->date]["consult"][$consult->_id] = $consult;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"              , CMbDT::date());
$smarty->assign("filter"             , $filter);
$smarty->assign("listPrat"           , $listPrat);
$smarty->assign("listConsults"       , $listConsults);
$smarty->assign("listConsults_date"  , $listConsults_date);

$smarty->display("print_noncote.tpl");
?>