<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CParamsPaie", "params_paie_id");
$do->createMsg = "Paramètres créés";
$do->modifyMsg = "Paramètres modifiés";
$do->deleteMsg = "Paramètres supprimés";
$do->doIt();
?>