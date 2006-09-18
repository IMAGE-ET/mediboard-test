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
$do->doIt();

?>