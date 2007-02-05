<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

$do = new CDoObjectAddEdit("CAddiction", "addiction_id");
$do->createMsg = "Addiction créée";
$do->modifyMsg = "Addiction modifiée";
$do->deleteMsg = "Addiction supprimée";
$do->doIt();

?>