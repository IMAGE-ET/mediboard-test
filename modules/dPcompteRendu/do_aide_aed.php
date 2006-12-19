<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/
global $AppUI, $m;

$do = new CDoObjectAddEdit("CAideSaisie", "aide_id");
$do->createMsg = "Aide créée";
$do->modifyMsg = "Aide modifiée";
$do->deleteMsg = "Aide supprimée";
$do->doIt();

?>