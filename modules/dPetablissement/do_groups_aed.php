<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CGroups", "group_id");
$do->createMsg = "Etablissement créé";
$do->modifyMsg = "Etablissement modifié";
$do->deleteMsg = "Etablissement supprimé";
$do->doIt();

?>