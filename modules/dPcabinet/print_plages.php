<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}

$deb  = mbGetValueFromGetOrSession("deb", mbDate());
$fin  = mbGetValueFromGetOrSession("fin", mbDate());
$chir = mbGetValueFromGetOrSession("chir");

// On selectionne les plages
$listPlage = new CPlageconsult;
$where = array();
$where["date"] = "BETWEEN '$deb' AND '$fin'";

// Liste des praticiens
$mediusers = new CMediusers();
$listPrat = $mediusers->loadPraticiens(PERM_EDIT);
$where["chir_id"] = db_prepare_in(array_keys($listPrat), $chir);

$order = array();
$order[] = "date";
$order[] = "chir_id";
$order[] = "debut";
$listPlage = $listPlage->loadList($where, $order);

// Pour chaque plage on selectionne les consultations
foreach($listPlage as $keyPlage => $plage) {
  $listPlage[$keyPlage]->loadRefs(false);
  $consultation =& $listPlage[$keyPlage]->_ref_consultations;
  foreach($consultation as $keyCons => $consult) {
    $consultation[$keyCons]->loadRefPatient();
    $consultation[$keyCons]->loadRefConsultAnesth();
    $consult_anesth =& $consultation[$keyCons]->_ref_consult_anesth;
    if($consult_anesth->consultation_anesth_id && $consult_anesth->operation_id){
      $consult_anesth->loadRefOperation();
      $consult_anesth->_ref_operation->loadRefsFwd();
      $consult_anesth->_date_op =& $consult_anesth->_ref_operation->_ref_plageop->date;
    } 
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("deb", $deb);
$smarty->assign("fin", $fin);
$smarty->assign("listPlage", $listPlage);

$smarty->display("print_plages.tpl");

?>