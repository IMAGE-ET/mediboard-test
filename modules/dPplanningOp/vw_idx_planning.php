<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers") );
require_once($AppUI->getModuleClass("dPplanningOp" , "planning"   ));
require_once($AppUI->getModuleClass("dPbloc"       , "plagesop"   ));
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));

if(!$canRead) {
	$AppUI->redirect("m=system&a=access_denied");
}

$date      = mbGetValueFromGetOrSession("date", mbDate());
$lastmonth = mbDate("-1 month", $date);
$nextmonth = mbDate("+1 month", $date);

$urgences = mbGetValueFromGetOrSession("urgences", 0);

// Sélection du praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

$selChir = mbGetValueFromGetOrSession("selChir", $mediuser->isPraticien() ? $mediuser->user_id : null);

$selPrat = new CMediusers();
$selPrat->load($selChir);

$selChirLogin = null;
$specialite = null;
if ($selPrat->isPraticien()) {
  $selChirLogin = $selPrat->user_id;
  $specialite = $selPrat->function_id;
}

// Tous les praticiens
$mediuser = new CMediusers;
$listChir = $mediuser->loadPraticiens(PERM_EDIT);

// récupération des modèles de compte-rendu disponibles
$crList = new CCompteRendu;
$where = array();
$where["chir_id"] = "= '$selChir'";
$where["type"] = "= 'operation'";
$order[] = "nom";
$crList = $crList->loadList($where, $order);
$hospiList = new CCompteRendu;
$where = array();
$where["chir_id"] = "= '$selChir'";
$where["type"] = "= 'hospitalisation'";
$order[] = "nom";
$hospiList = $hospiList->loadList($where, $order);

// Planning du mois
$sql = "SELECT plagesop.*," .
		"\nSEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree," .
		"\nCOUNT(operations.operation_id) AS total" .
		"\nFROM plagesop" .
		"\nLEFT JOIN operations" .
		"\nON plagesop.id = operations.plageop_id" .
    "\nAND operations.annulee = 0" .
		"\nWHERE (plagesop.chir_id = '$selChirLogin' OR plagesop.id_spec = '$specialite')" .
		"\nAND plagesop.date LIKE '".mbTranformTime("+ 0 day", $date, "%Y-%m")."-__'" .
		"\nGROUP BY plagesop.id" .
		"\nORDER BY plagesop.date, plagesop.debut, plagesop.id";
if($selChirLogin)
  $listPlages = db_loadList($sql);
else
  $listPlages = null;

// Urgences du mois
$listUrgences = new COperation;
$where = array();
$where["date"] = "LIKE '".mbTranformTime("+ 0 day", $date, "%Y-%m")."-__'";
$where["chir_id"] = "= '$selChirLogin'";
$order = "date";
$listUrgences = $listUrgences->loadList($where, $order);
if($urgences) {
  foreach($listUrgences as $keyUrg => $curr_urg) {
    $listUrgences[$keyUrg]->loadRefs();
    $listUrgences[$keyUrg]->_ref_sejour->loadRefsFwd();
  }
}

// Liste des opérations du jour sélectionné
$listDay = new CPlageOp;
$where = array();
$where["date"] = "= '$date'";
$where["chir_id"] = "= '$selChirLogin'";
$order = "debut";
$listDay = $listDay->loadList($where, $order);
foreach($listDay as $key => $value) {
  $listDay[$key]->loadRefs();
  foreach($listDay[$key]->_ref_operations as $key2 => $value2) {
    $listDay[$key]->_ref_operations[$key2]->loadRefs();
    $listDay[$key]->_ref_operations[$key2]->_ref_sejour->loadRefsFwd();
  }
}


// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("date"        , $date        );
$smarty->assign("lastmonth"   , $lastmonth   );
$smarty->assign("nextmonth"   , $nextmonth   );
$smarty->assign("listChir"    , $listChir    );
$smarty->assign("selChir"     , $selChir     );
$smarty->assign("crList"      , $crList      );
$smarty->assign("hospiList"   , $hospiList   );
$smarty->assign("listPlages"  , $listPlages  );
$smarty->assign("listDay"     , $listDay     );
$smarty->assign("listUrgences", $listUrgences);
$smarty->assign("urgences"    , $urgences);

$smarty->display("vw_idx_planning.tpl");

?>