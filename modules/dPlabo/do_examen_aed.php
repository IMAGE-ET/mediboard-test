<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CExamenLabo", "examen_labo_id");
$do->createMsg = "Analyse créée";
$do->modifyMsg = "Analyse modifiée";
$do->deleteMsg = "Analyse supprimée";
$do->doIt();

?>