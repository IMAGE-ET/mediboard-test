<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CLit", "lit_id");
$do->createMsg = "Lit créé";
$do->modifyMsg = "Lit modifié";
$do->deleteMsg = "Lit supprimé";
$do->doIt();
?>