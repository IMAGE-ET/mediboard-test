<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $utypes;

$can->needsRead();

// Récupération du user à ajouter/editer
$mediuserSel = new CMediusers;
$mediuserSel->load(mbGetValueFromGetOrSession("user_id"));

// Chargement des banques
$order = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null, $order);

// Récupération des fonctions
$groups = new CGroups;
$order = "text";
$groups = $groups->loadList(null, $order);
foreach ($groups as &$group) {
  $group->loadRefsBack();
  foreach ($group->_ref_functions as &$function){
    // Récuperation des utilisateurs
    $function->loadRefs();
  }
}

// Récupération des disciplines
$disciplines = new CDiscipline;
$disciplines = $disciplines->loadList();

// Récupération des spécialités CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList();
  
// Récupération des profils
$where = array (
    "template" => "= '1'"
);
$profiles = new CUser();
$profiles = $profiles->loadList($where);

// Creation du tableau de profil en fonction du type
foreach($profiles as $key => $profil){
  $tabProfil[$profil->user_type][] = $profil->_id;
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tabProfil"    , $tabProfil    );
$smarty->assign("utypes"       , $utypes       );
$smarty->assign("banques"      , $banques      );
$smarty->assign("mediuserSel"  , $mediuserSel  );
$smarty->assign("profiles"     , $profiles     );
$smarty->assign("groups"       , $groups       );
$smarty->assign("disciplines"  , $disciplines  );
$smarty->assign("spec_cpam"    , $spec_cpam    );

$smarty->display("vw_idx_mediusers.tpl");

?>