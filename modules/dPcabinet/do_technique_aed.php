<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

require_once( $AppUI->getModuleClass("dPcabinet", "techniqueComp") );
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CTechniqueComp", "technique_id");
$do->createMsg = "Technique complémentaire créé";
$do->modifyMsg = "Technique complémentaire modifié";
$do->deleteMsg = "Technique complémentaire supprimé";
$do->doIt();

?>