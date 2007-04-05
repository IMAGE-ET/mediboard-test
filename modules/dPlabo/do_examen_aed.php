<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CExamenLabo", "examen_labo_id");
$do->createMsg = "Examen créé";
$do->modifyMsg = "Examen modifié";
$do->deleteMsg = "Examen supprimé";
$do->doIt();

?>