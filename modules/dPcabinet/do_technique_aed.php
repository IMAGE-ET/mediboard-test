<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CTechniqueComp", "technique_id");
$do->createMsg = "Technique complémentaire créé";
$do->modifyMsg = "Technique complémentaire modifié";
$do->deleteMsg = "Technique complémentaire supprimé";
$do->doIt();

?>