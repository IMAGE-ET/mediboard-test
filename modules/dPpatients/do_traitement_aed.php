<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CTraitement", "traitement_id");
$do->createMsg = "Traitement créé";
$do->modifyMsg = "Traitement modifié";
$do->deleteMsg = "Traitement supprimé";
$do->doIt();

?>