<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CGestionCab", "gestioncab_id");
$do->createMsg = "Fiche créée";
$do->modifyMsg = "Fiche modifiée";
$do->deleteMsg = "Fiche supprimée";
$do->doIt();
?>