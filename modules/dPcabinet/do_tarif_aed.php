<?php /* $Id: do_tarif_aed.php,v 1.2 2005/10/04 10:56:49 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.2 $
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