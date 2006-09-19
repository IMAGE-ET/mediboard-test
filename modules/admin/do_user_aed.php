<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CUser", "user_id");

$do->createMsg = "Utilisateur créé";
$do->modifyMsg = "Utilisateur modifié";
$do->deleteMsg = "Utilisateur supprimé";

$do->doBind();
    
if (intval(dPgetParam($_POST, 'del'))) {
  $do->doDelete();
} else {
  // Verification de la non existence d'un utilisateur avec le même login
  $otherUser = new CUser;
  $where = array();
  $where["user_username"] = "= '".$do->_obj->user_username."'";
  $where["user_id"] = "!= '".$do->_obj->user_id."'";
  $otherUser->loadObject($where);
  if($otherUser->user_id) {
    $AppUI->setMsg("Login déjà existant dans la base", UI_MSG_ERROR);
  } else {
    $do->doStore();
  }
}
$do->doRedirect();

?>