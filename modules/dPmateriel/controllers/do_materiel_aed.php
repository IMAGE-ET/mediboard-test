<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPmateriel
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CMateriel", "materiel_id");
$do->createMsg = "Matériel créé";
$do->modifyMsg = "Matériel modifié";
$do->deleteMsg = "Matériel supprimé";
$do->doIt();

?>