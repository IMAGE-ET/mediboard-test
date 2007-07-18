<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;
$ds = CSQLDataSource::get("std");

$old_pwd  = mbGetValueFromPost("old_pwd"  , null);
$new_pwd1 = mbGetValueFromPost("new_pwd1" , null);
$new_pwd2 = mbGetValueFromPost("new_pwd2" , null);

// Vérification du mot de passe actuel de l'utilisateur courant
$user = new CUser;
$where = array();
$where["user_id"]       = $ds->prepare("= %", $AppUI->user_id);
$where["user_password"] = $ds->prepare("= %", md5($old_pwd));

$user->loadObject($where);

if($user->_id){
  // Mot de passe actuel correct
  if($new_pwd1 == $new_pwd2){
    $user->_user_password = $new_pwd1;
    $user->store();
    $AppUI->setMsg("chgpwUpdated", UI_MSG_OK);
  }else{
    // Nouveaux mot de passe différents
    $AppUI->setMsg("chgpwNoMatch", UI_MSG_ERROR);
  }
}else{
  // Mauvais mot de passe actuel
  $AppUI->setMsg("chgpwWrongPW", UI_MSG_ERROR);
}
?>