<?php /* $Id: vw_idx_mediusers.php 7695 2009-12-23 09:10:10Z rhum1 $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: 7695 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$user_id = CValue::getOrSession("user_id");

// Récupération des fonctions
$group = CGroups::loadCurrent();
$group->loadFunctions();

$object = new CMediusers;
$object->load($user_id);
$object->loadRefFunction();
$object->loadRefProfile();

// Chargement des banques
$banques = array();
if (class_exists("CBanque")) {
  $order = "nom ASC";
  $banque = new CBanque();
  $banques = $banque->loadList(null, $order);
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
$smarty->assign("utypes"       , CUser::$types );
$smarty->assign("banques"      , $banques      );
$smarty->assign("object"       , $object       );
$smarty->assign("profiles"     , $profiles     );
$smarty->assign("group"        , $group        );
$smarty->assign("disciplines"  , $disciplines  );
$smarty->assign("spec_cpam"    , $spec_cpam    );

$smarty->display("inc_edit_mediuser.tpl");

?>