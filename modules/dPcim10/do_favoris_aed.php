<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/


$do = new CDoObjectAddEdit("CFavoricim10", "favoris_id");

// Amélioration des textes
$user = new CMediusers;
$user->load($_POST["favoris_user"]);
$for = " pour $user->_view";
$do->createMsg .= $for;
$do->modifyMsg .= $for;
$do->deleteMsg .= $for;

$do->redirect = null;
$do->doIt();

?>