<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Droit de lecture dPsante400
$moduleSante400 = CModule::getInstalled("dPsante400");
$canReadSante400 = $moduleSante400 ? $moduleSante400->canRead() : false;

// Récupération du user à ajouter/editer
$mediuserSel = new CMediusers;
$mediuserSel->load(mbGetValueFromGetOrSession("user_id"));

// Récupération des fonctions
$order = array("group_id", "text");
$functions = new CFunctions;
$functions = $functions->loadList(null, $order);

// Récupération des disciplines
$disciplines = new CDiscipline;
$disciplines = $disciplines->loadList();

// Récupération des spécialités CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList();

// Récuperation des utilisateurs
foreach ($functions as $key => $function) {
  $functions[$key]->loadRefs();
}
  
// Récupération des profils
$where = array (
  "user_username" => "LIKE '>> %'"
);
$profiles = new CUser();
$profiles = $profiles->loadList($where);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("canReadSante400", $canReadSante400);
$smarty->assign("mediuserSel"    , $mediuserSel    );
$smarty->assign("profiles"       , $profiles       );
$smarty->assign("functions"      , $functions      );
$smarty->assign("disciplines"    , $disciplines    );
$smarty->assign("spec_cpam"      , $spec_cpam      );

$smarty->display("vw_idx_mediusers.tpl");

?>