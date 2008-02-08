<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CPlageconsult", "plageconsult_id");
$do->createMsg = "Plage créée";
$do->modifyMsg = "Plage modifiée";
$do->deleteMsg = "Plage supprimée";
$do->doIt();

?>