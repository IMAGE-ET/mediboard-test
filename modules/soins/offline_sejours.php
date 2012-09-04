<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$profile = 0;

if ($profile) {
  xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY, array(
    'ignored_functions' => array(
      'call_user_func',
      'call_user_func_array',
    )
  ));
}

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

set_min_memory_limit("640M");
set_time_limit(120);

$service_id = CValue::get("service_id");
$date       = CValue::get("date", mbDate());
$mode       = CValue::get("mode", 0);

$service = new CService;
$service->load($service_id);
loadServiceComplet($service, $date, $mode);

$sejours = array();
$dossiers_complets = array();

foreach($service->_ref_chambres as &$_chambre){
  foreach($_chambre->_ref_lits as &$_lits){
    foreach($_lits->_ref_affectations as &$_affectation){
      $sejour = $_affectation->_ref_sejour;
      $sejours[]= $sejour;
      
      // Détail du séjour
      $sejour->checkDaysRelative($date);
      $sejour->loadNDA();
      $sejour->loadRefsNotes();
      $sejour->loadRefCurrAffectation()->loadRefLit();
      
      // Patient
      $patient = $sejour->loadRefPatient();
      $patient->loadIPP();
      
      $params = array(
        "sejour_id" => $sejour->_id,
        "dialog" => 1,
        "offline" => 1,
        "in_modal" => 1
      );
      
      $dossiers_complets[$sejour->_id] = CApp::fetch("soins", "print_dossier_soins", $params);
    }
  }
}

if ($profile) {
  $xhprof_data = xhprof_disable();
  $xhprof_root = 'C:/xampp/htdocs/xhgui/';
  require_once $xhprof_root.'xhprof_lib/config.php';
  require_once $xhprof_root.'xhprof_lib/utils/xhprof_lib.php';
  require_once $xhprof_root.'xhprof_lib/utils/xhprof_runs.php';
  
  $xhprof_runs = new XHProfRuns_Default();
  $run_id = $xhprof_runs->save_run($xhprof_data, "mediboard");
}

$smarty = new CSmartyDP;
$smarty->assign("date", $date);
$smarty->assign("hour", mbTime());
$smarty->assign("service", $service);
$smarty->assign("sejours", $sejours);
$smarty->assign("dossiers_complets", $dossiers_complets);
$smarty->display("offline_sejours.tpl");

?>