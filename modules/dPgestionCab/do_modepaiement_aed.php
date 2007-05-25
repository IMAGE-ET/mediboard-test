<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Poiron Yohann	
 */

global $AppUI;

$do = new CDoObjectAddEdit("CModePaiement", "mode_paiement_id");
$do->createMsg = "Mode de paiement créée";
$do->modifyMsg = "Mode de paiement modifiée";
$do->deleteMsg = "Mode de paiement supprimée";
$do->doIt();

?>