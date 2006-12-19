<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CTypeRepas", "typerepas_id");
$do->createMsg = "Type de repas créé";
$do->modifyMsg = "Type de repas modifié";
$do->deleteMsg = "Type de repas supprimé";
$do->doIt();

?>