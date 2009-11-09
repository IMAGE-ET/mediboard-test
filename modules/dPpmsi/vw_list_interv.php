<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$date = CValue::getOrSession("date", mbDate());

// Selection des salles
$listSalles = new CSalle;
$listSalles = $listSalles->loadGroupList();

$totalOp = 0;

$counts = array (
  "operations" => array (
	  "total" => 0,
    "facturees" => 0,
	),
  "urgences" => array (
    "total" => 0,
    "facturees" => 0,
	),
);

$plages = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$order = "debut";
$plages = $plages->loadList($where, $order);
foreach($plages as &$_plage) {
  $_plage->loadRefsOperations(0);
  foreach($_plage->_ref_operations as $_operation) {
    $_operation->loadRefChir();
    $_operation->loadRefSejour();
    $_operation->_ref_sejour->loadNumDossier();
    $_operation->_ref_sejour->loadRefPatient();
    $_operation->_ref_sejour->_ref_patient->loadIPP();
    $_operation->loadExtCodesCCAM();
    $_operation->loadHprimFiles();

		$counts["operations"]["total"]++; 
		if (count($_operation->_ref_hprim_files)) {
      $counts["operations"]["facturees"]++; 
		}
  }
  $totalOp += count($_plage->_ref_operations);
}

$operation = new COperation;
$where = array();
$where["date"]     = "= '$date'";
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$where["annulee"]  = "= '0'";
$order = "chir_id";
$urgences = $operation->loadList($where, $order);
$totalOp += count($urgences);
foreach($urgences as $_operation) {
  $_operation->loadRefChir();
  $_operation->loadRefSejour();
  $_operation->_ref_sejour->loadNumDossier();
  $_operation->_ref_sejour->loadRefPatient();
  $_operation->_ref_sejour->_ref_patient->loadIPP();
  $_operation->loadExtCodesCCAM();
  $_operation->loadHprimFiles();
	
  $counts["urgences"]["total"]++; 
  if (count($_operation->_ref_hprim_files)) {
    $counts["urgences"]["facturees"]++; 
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("date"     , $date);
$smarty->assign("operation", $operation);
$smarty->assign("plages"   , $plages);
$smarty->assign("urgences" , $urgences);
$smarty->assign("counts"   , $counts);
$smarty->assign("totalOp"  , $totalOp);

$smarty->display("vw_list_interv.tpl");

?>
