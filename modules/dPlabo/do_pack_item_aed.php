<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPackItemExamenLabo", "pack_item_examen_labo_id");
$do->createMsg = "Examen ajouté";
$do->modifyMsg = "Examen modifié";
$do->deleteMsg = "Examen supprimé";
$do->doIt();

?>