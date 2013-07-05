<?php

/**
 * Edit mediuser
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$user_id = CValue::getOrSession("user_id");

// Récupération des fonctions
$group = CGroups::loadCurrent();
$group->loadFunctions();

// Récupération du user à ajouter/editer 
$object = new CMediusers;
if (CValue::get("no_association")) {
  $object->user_id = $user_id;
  $object->updateFormFields();
  $object->_user_id     = $user_id;
  $object->_id          = null;
  $object->actif        = CValue::get("ldap_user_actif", 1);
  $object->deb_activite = CValue::get("ldap_user_deb_activite");;
  $object->fin_activite = CValue::get("ldap_user_fin_activite");;
}
else {
  $object->load($user_id);
  $object->loadRefFunction();
  $object->loadRefProfile();
}

$object->loadNamedFile("identite.jpg");
$object->loadNamedFile("signature.jpg");

// Savoir s'il est relié au LDAP
if (isset($object->_ref_user)) {
  $object->_ref_user->isLDAPLinked();
}

// Chargement des banques
$banques = array();
if (class_exists("CBanque")) {
  $order = "nom ASC";
  $banque = new CBanque();
  $banques = $banque->loadList(null, $order);
}

// Récupération des disciplines
$discipline = new CDiscipline;
$disciplines = $discipline->loadList();

// Récupération des spécialités CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList();
  
// Récupération des profils
$profile = new CUser();
$profile->template = 1;
/** @var CUser[] $profiles */
$profiles = $profile->loadMatchingList();

// Creation du tableau de profil en fonction du type
$tabProfil = array();
foreach ($profiles as $profil) {
  $tabProfil[$profil->user_type][] = $profil->_id;
}

$tag = false;
if ($object->_id) {
  $tag = CIdSante400::getMatch($object->_class, CMediusers::getTagSoftware(), null, $object->_id)->id400;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tabProfil"   , $tabProfil);
$smarty->assign("utypes"      , CUser::$types);
$smarty->assign("ps_types"    , CUser::$ps_types);
$smarty->assign("banques"     , $banques);
$smarty->assign("object"      , $object);
$smarty->assign("profiles"    , $profiles);
$smarty->assign("group"       , $group);
$smarty->assign("disciplines" , $disciplines);
$smarty->assign("spec_cpam"   , $spec_cpam);
$smarty->assign("tag_mediuser", CMediusers::getTagMediusers($group->_id));
$smarty->assign("is_admin",     CAppUI::$user->isAdmin());
$smarty->assign("is_robot",     $object->isRobot());
$smarty->assign("tag",          $tag);

$smarty->display("inc_edit_mediuser.tpl");