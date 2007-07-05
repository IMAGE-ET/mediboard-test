<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPackItemExamenLabo", "pack_item_examen_labo_id");
$do->createMsg = "Analyse ajoutée";
$do->modifyMsg = "Analyse modifiée";
$do->deleteMsg = "Analyse enlevée";
$do->doIt();

?>