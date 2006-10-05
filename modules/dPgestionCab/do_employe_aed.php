<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CEmployeCab", "employecab_id");
$do->createMsg = "Employé créé";
$do->modifyMsg = "Employé modifié";
$do->deleteMsg = "Employé supprimé";
$do->doIt();
?>