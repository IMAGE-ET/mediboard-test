<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Alexis / Yohann	
 */

global $AppUI;

$do = new CDoObjectAddEdit("CFacture", "facture_id");
$do->createMsg = "Facture créée";
$do->modifyMsg = "Facture modifiée";
$do->deleteMsg = "Facture supprimée";
$do->doIt();

?>