<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {			// lock out users that do not have at least readPermission on this module
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );

$deb = dPgetParam( $_GET, 'deb', date("Y-m-d")." 06:00:00");
$fin = dPgetParam( $_GET, 'fin', date("Y-m-d")." 21:00:00");
$service = dPgetParam( $_GET, 'service', 0);
$type = dPgetParam( $_GET, 'type', 0 );
$chir = dPgetParam( $_GET, 'chir', 0 );
$spe = dPgetParam( $_GET, 'spe', 0);
$conv = dPgetParam( $_GET, 'conv', 0);
$ordre = dPgetParam( $_GET, 'ordre', 'heure');
$total = 0;


$where[] = "DATE_ADD(`date_adm`, INTERVAL `time_adm` HOUR_SECOND) >= '$deb'";
$where[] = "DATE_ADD(`date_adm`, INTERVAL `time_adm` HOUR_SECOND) <= '$fin'";

$whereImploded = implode(" AND ", $where);

// On sort les journées
$sql = "SELECT date_adm" .
    "\nFROM operations" .
    "\nWHERE $whereImploded" .
    "\nGROUP BY date_adm" .
    "\nORDER BY date_adm";
$listDays = db_loadlist($sql);

// Clause de filtre par spécialité
if ($spe) {
  $speChirs = new CMediusers;
  $speChirs = $speChirs->loadList(array ("function_id" => "= '$spe'"));
  $idChirs = join(array_keys($speChirs), ", ");
  $inChirs = "AND chir_id IN ($idChirs)";
}

// Clause de filtre par chirurgien
$addChir = $chir ? " AND chir_id = '$chir'" : null;

// On sort les chirurgiens de chaque jour
foreach($listDays as $keyDay => $valueDay) {
  $day =& $listDays[$keyDay];
  $sql = "SELECT chir_id, user_last_name, user_first_name" .
  		"\nFROM operations" .
  		"\nLEFT JOIN users" .
  		"\nON users.user_id = operations.chir_id" .
  		"\nWHERE date_adm = '".$day["date_adm"]."'" .
      "\nAND $whereImploded";

  if ($spe) {
    $sql .= $inChirs;
  }

  if ($chir) {
    $sql .= $addChir;
  }
  
  $sql .= " GROUP BY chir_id" .
  		" ORDER BY chir_id";
      
  $day["listChirs"] = db_loadlist($sql);
  foreach($day["listChirs"] as $keyChir => $valueChir) {
    $dayChir =& $day["listChirs"][$keyChir];
  	$listAdm = new COperation;
  	$ljoin = array();
  	$ljoin["patients"] = "operations.pat_id = patients.patient_id";
  	$where = array();
  	$where["annulee"] = "= 0";
  	$where["chir_id"] = "= '". $valueChir["chir_id"] ."'";
  	$where["date_adm"] = "= '".$day["date_adm"]."'";

    if ($type) {
      $where["type_adm"] = "= '$type'";
    }
    
    if ($conv == "o") {
        $where[] = "(operations.convalescence IS NOT NULL AND operations.convalescence != '')";
    }
    
    if ($conv == "n") {
        $where[] = "(operations.convalescence IS NULL OR operations.convalescence = '')";
    }
    
  	$where[] = $whereImploded;
    if($ordre == 'heure')
      $order = "operations.time_adm, operations.chir_id, operations.time_operation";
    else
      $order = "patients.nom, patients.prenom, operations.chir_id, operations.time_adm";
  	$listAdm = $listAdm->loadList($where, $order, null, null, $ljoin);
    $dayChir["admissions"] = array();
    
    foreach ($listAdm as $keyAdm => $valueAdm) {
      $operation =& $listAdm[$keyAdm];
      $operation->loadRefs();
      
      $first_affectation =& $operation->_ref_first_affectation;
      if ($first_affectation->affectation_id) {
        $first_affectation->loadRefsFwd();
        $lit =& $first_affectation->_ref_lit;
        $lit->loadRefsFwd();
        $chambre =& $lit->_ref_chambre;
        $chambre->loadRefsFwd();
      }
      
      if (!$service || (isset($chambre) && $chambre->_ref_service->service_id == $service)) {
        $dayChir["admissions"][$keyAdm] = $operation;
      }
    }
    
    $total += count($dayChir["admissions"]);
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('listDays', $listDays);
$smarty->assign('total', $total);

$smarty->display('print_planning.tpl');

?>