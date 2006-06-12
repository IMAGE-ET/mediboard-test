

<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {      // lock out users that do not have at least readPermission on this module
  $AppUI->redirect( "m=system&a=access_denied" );
}

require_once( $AppUI->getModuleClass('dPplanningOp', 'sejour') );

$deb = dPgetParam( $_GET, 'deb', date("Y-m-d")." 06:00:00");
$fin = dPgetParam( $_GET, 'fin', date("Y-m-d")." 21:00:00");
$service = dPgetParam( $_GET, 'service', 0);
$type = dPgetParam( $_GET, 'type', 0 );
$chir = dPgetParam( $_GET, 'chir', 0 );
$spe = dPgetParam( $_GET, 'spe', 0);
$conv = dPgetParam( $_GET, 'conv', 0);
$ordre = dPgetParam( $_GET, 'ordre', 'heure');
$total = 0;

$sejours = new CSejour;

$ljoin["patients"] = "patients.patient_id = sejour.patient_id";

$where[] = "sejour.entree_prevue >= '$deb'";
$where[] = "sejour.entree_prevue <= '$fin'";
$where["annule"] = "= 0";
// Clause de filtre par spécialité
if ($spe) {
  $speChirs = new CMediusers;
  $speChirs = $speChirs->loadList(array ("function_id" => "= '$spe'"));
  $idChirs = join(array_keys($speChirs), ", ");
  $where[] = "sejour.praticien_id IN ($idChirs)";
}
// Clause de filtre par chirurgien
if($chir) {
  $where[] = "sejour.praticien_id = '$chir'";
}
if ($type) {
  $where["type"] = "= '$type'";
}
if ($conv == "o") {
  $where[] = "(sejour.convalescence IS NOT NULL AND sejour.convalescence != '')";
}
if ($conv == "n") {
  $where[] = "(sejour.convalescence IS NULL OR sejour.convalescence = '')";
}

$order = "DATE_FORMAT(sejour.entree_prevue, '%Y-%m-%d'), sejour.praticien_id";
if($ordre == "heure") {
  $order .= ", sejour.entree_prevue";
} else {
  $order .= ", patients.nom, patients.prenom";
}

$sejours = $sejours->loadList($where, $order, null, null, $ljoin);

$listDays = array();
$listPrats = array();
if(count($sejours)) {
  foreach($sejours as $key => $sejour) {
    $sejours[$key]->loadRefs();
    $sejours[$key]->_ref_first_affectation->loadRefsFwd();
    $sejours[$key]->_ref_first_affectation->_ref_lit->loadCompleteView();
    if($service && ($sejours[$key]->_ref_first_affectation->_ref_lit->_ref_chambre->_ref_service->service_id != $service)) {
      unset($sejours[$key]);
    } else {
      if(!isset($listPrats[$sejour->praticien_id])) {
        $listPrats[$sejour->praticien_id] =& $sejours[$key]->_ref_praticien;
      }
      foreach($sejours[$key]->_ref_operations as $keyOp => $operation) {
        $sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
      }
      $curr_date = mbDate(null, $sejour->entree_prevue);
      $curr_prat = $sejour->praticien_id;
      $listDays[$curr_date][$curr_prat][] =& $sejours[$key];
    }
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('listDays', $listDays);
$smarty->assign('listPrats', $listPrats);
$smarty->assign('total', count($sejours));

$smarty->display('print_planning.tpl');

?>