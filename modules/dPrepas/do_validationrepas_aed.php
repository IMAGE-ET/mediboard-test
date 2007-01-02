<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CValidationRepas", "validationrepas_id");
$do->createMsg = "Validation des repas créée";
$do->modifyMsg = "Validation des repas modifiée";
$do->deleteMsg = "Validation des repas supprimée";
$do->doIt();
?>