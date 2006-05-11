<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPpmsi
 *  @version $Revision: $
 *  @author Romain Ollivier
 */

global $AppUI;

require_once($AppUI->getModuleClass('dPpmsi', 'GHM'));
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CGHM", "ghm_id");
$do->createMsg = "GHM créée";
$do->modifyMsg = "GHM modifiée";
$do->deleteMsg = "GHM supprimée";
$do->doIt();

?>