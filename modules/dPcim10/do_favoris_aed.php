<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

mbDump($_POST, "POST");
$user = new CMediusers;
$user->load($_POST["favoris_user"]);
mbDump($user->_view, "Utilisateur");

$do = new CDoObjectAddEdit("CFavoricim10", "favoris_id");
$do->redirect = null;
$do->doIt();

?>