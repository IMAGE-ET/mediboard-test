<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPcabinet", "tarif"));
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CTarif", "tarif_id");
$do->createMsg = "Tarif créé";
$do->modifyMsg = "Tarif modifié";
$do->deleteMsg = "Tarif supprimé";
$do->doIt();

?>