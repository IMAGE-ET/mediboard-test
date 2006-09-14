<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

$do = new CDoObjectAddEdit("CTarif", "tarif_id");
$do->createMsg = "Tarif créé";
$do->modifyMsg = "Tarif modifié";
$do->deleteMsg = "Tarif supprimé";
$do->doIt();

?>