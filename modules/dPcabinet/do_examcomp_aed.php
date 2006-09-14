<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CExamComp", "exam_id");
$do->createMsg = "Examen complémentaire créé";
$do->modifyMsg = "Examen complémentaire modifié";
$do->deleteMsg = "Examen complémentaire supprimé";
$do->doIt();

?>