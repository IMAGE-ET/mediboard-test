<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Poiron Yohann	
 */

global $AppUI;

$do = new CDoObjectAddEdit("CRubrique", "rubrique_id");
$do->createMsg = "Rubrique créée";
$do->modifyMsg = "Rubrique modifiée";
$do->deleteMsg = "Rubrique supprimée";
$do->doIt();

?>