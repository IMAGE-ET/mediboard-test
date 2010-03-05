<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPmateriel
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CCommandeMateriel", "commande_materiel_id");
$do->createMsg = "Commande créée";
$do->modifyMsg = "Commande modifiée";
$do->deleteMsg = "Commande supprimée";
$do->doIt();

?>