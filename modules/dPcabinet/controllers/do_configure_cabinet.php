<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$group = CGroups::loadCurrent();
$group_id = $group->_id;

// --------------------------------
// cabinet
$cabinet_text     = CValue::post("text");
$compta_partagee  = CValue::post("compta_partagee");
$consult_partagee = CValue::post("consults_partagees");
$cabinet_adresse  = CValue::post("adresse");
$cabinet_ville    = CValue::post("ville");
$cabinet_cp       = CValue::post("cp");

$cabinet = new CFunctions();
$cabinet->group_id = $group_id;
$cabinet->type = "cabinet";
$cabinet->text = $cabinet_text;
$cabinet->loadMatchingObject();

//exist ? problem !
if ($cabinet->_id) {
  CAppUI::stepAjax("Cabinet-already_exist_name%s", UI_MSG_ERROR, $cabinet_text);
}
else {
  $cabinet->actif = 1;
  $cabinet->facturable = 1;
  $cabinet->cp = $cabinet_cp;
  $cabinet->ville = $cabinet_ville;
  $cabinet->adresse = $cabinet_adresse;
  $cabinet->compta_partagee = $compta_partagee;
  $cabinet->consults_partagees = $consult_partagee;
}

if ($msg = $cabinet->store()) {
  CAppUI::stepAjax($msg, UI_MSG_ERROR);
}

CAppUI::stepAjax("Cabinet_msg_cabinet%s_created_num%d", UI_MSG_OK, $cabinet->text, $cabinet->_id);

// --------------------------------
// praticien(s) && secretaires

$profile_prat = CValue::post("profile_prat");
$profile_sec = CValue::post("profile_sec");

foreach ($_POST["user"] as $type => $_user_list) {
  foreach ($_user_list as $_user) {
    if (!$_user['lastname'] || !$_user['firstname']) {
      continue;
    }
    $mediuser = new CMediusers();
    $mediuser->_user_last_name  = trim($_user['lastname']);
    $mediuser->_user_first_name = trim($_user['firstname']);
    $mediuser->makeUsernamePassword($mediuser->_user_first_name, $mediuser->_user_last_name);
    $where = array();
    $ljoin["users"] = "users_mediboard.user_id = users.user_id";
    $where['users.user_username'] = "= '".$mediuser->_user_username."'";
    $mediuser->loadObject($where, null, null, $ljoin);
    if ($mediuser->_id) {
      CAppUI::stepAjax("Cabinet_mediuser_prenom%s_nom%s_username%s_already_exist", UI_MSG_WARNING, $mediuser->_user_first_name, $mediuser->_user_last_name, $mediuser->_user_username);
      continue;
    }

    $mediuser->function_id = $cabinet->_id;

    //praticien
    if ($type == "prat") {
      $mediuser->_profile_id = $profile_prat;
      $mediuser->_user_type = 13;
    }

    //secretaire
    if ($type == "sec") {
      $mediuser->_profile_id = $profile_sec;
      $mediuser->_user_type = 10;
    }

    //store
    if ($msg = $mediuser->store()) {
      CAppUI::stepAjax($msg, UI_MSG_WARNING);
      continue;
    }

    CAppUI::stepAjax("Cabinet_user_created_name%s_firstname%s_username%s_with_password_%s", UI_MSG_OK, $mediuser->_user_last_name, $mediuser->_user_first_name, $mediuser->_user_username, $mediuser->_user_password);
  }
}

CAppUI::callbackAjax("changePagePrimaryUsers", $cabinet->_id);