<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
$now       = mbDate();

$filter = new CConsultation;
$filter->_date_min = mbGetValueFromGet("_date_min"    , "$now");
$filter->_date_max = mbGetValueFromGet("_date_max"    , "$now");
$filter->_coordonnees = mbGetValueFromGet("_coordonnees");

$chir = mbGetValueFromGetOrSession("chir");

// On selectionne les plages
$listPlage = new CPlageconsult;
$where = array();
$where["date"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";

// Liste des praticiens
$mediusers = new CMediusers();
$listPrat = $mediusers->loadPraticiens(PERM_EDIT);
$where["chir_id"] = $ds->prepareIn(array_keys($listPrat), $chir);

$order = array();
$order[] = "date";
$order[] = "chir_id";
$order[] = "debut";
$listPlage = $listPlage->loadList($where, $order);

// Pour chaque plage on selectionne les consultations
foreach($listPlage as $keyPlage => $plage) {
  $listPlage[$keyPlage]->loadRefs(false, 1);
  $consultation =& $listPlage[$keyPlage]->_ref_consultations;
  foreach($consultation as $keyCons => $consult) {
    $consultation[$keyCons]->loadRefPatient(1);
    $consultation[$keyCons]->loadRefCategorie(1);
    $consultation[$keyCons]->loadRefConsultAnesth();
    $consult_anesth =& $consultation[$keyCons]->_ref_consult_anesth;
    if($consult_anesth->operation_id){
      $consult_anesth->loadRefOperation();
      $consult_anesth->_ref_operation->loadRefPraticien(true);
      $consult_anesth->_ref_operation->loadRefPlageOp(true);
      $consult_anesth->_date_op =& $consult_anesth->_ref_operation->_ref_plageop->date;
    } 
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("coordonnees", $filter->_coordonnees);
$smarty->assign("filter"     , $filter);
$smarty->assign("listPlage"  , $listPlage);

$smarty->display("print_plages.tpl");

?>