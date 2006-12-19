<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPlat", "plat_id");
$do->createMsg = "Plat créé";
$do->modifyMsg = "Plat modifié";
$do->deleteMsg = "Plat supprimé";
$do->doIt();

?>