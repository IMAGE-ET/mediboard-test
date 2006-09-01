<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

require_once( $AppUI->getModuleClass("dPcabinet", "examComp") );
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CExamComp", "exam_id");
$do->createMsg = "Examen complémentaire créé";
$do->modifyMsg = "Examen complémentaire modifié";
$do->deleteMsg = "Examen complémentaire supprimé";
$do->doIt();

?>