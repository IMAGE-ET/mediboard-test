<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPhospi", "affectation"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CAffectation", "affectation_id");
$do->createMsg = "Affectation créée";
$do->modifyMsg = "Affectation modifiée";
$do->deleteMsg = "Affectation supprimée";
$do->doIt();
?>