<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CRefMateriel", "reference_id");
$do->createMsg = "Référence créée";
$do->modifyMsg = "Référence modifiée";
$do->deleteMsg = "Référence supprimée";
$do->doIt();

?>