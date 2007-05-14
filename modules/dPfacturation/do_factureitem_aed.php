<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Alexis	
 */

global $AppUI;

$do = new CDoObjectAddEdit("CFactureItem", "factureitem_id");
$do->createMsg = "Elément créé";
$do->modifyMsg = "Elément modifié";
$do->deleteMsg = "Elément supprimé";
$do->doIt();

?>