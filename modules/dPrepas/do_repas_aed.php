<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CRepas", "repas_id");
$do->createMsg = "Repas créé";
$do->modifyMsg = "Repas modifié";
$do->deleteMsg = "Repas supprimé";
$do->doIt();

?>