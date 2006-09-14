<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CChambre", "chambre_id");
$do->createMsg = "Chambre créée";
$do->modifyMsg = "Chambre modifiée";
$do->deleteMsg = "Chambre supprimée";
$do->doIt();
?>