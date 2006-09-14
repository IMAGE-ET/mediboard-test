<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CService", "service_id");
$do->createMsg = "Service créé";
$do->modifyMsg = "Service modifié";
$do->deleteMsg = "Service supprimé";
$do->doIt();
?>