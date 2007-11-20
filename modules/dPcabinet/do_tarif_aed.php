<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/


global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CTarif", "tarif_id");
$do->createMsg = "Tarif créé";
$do->modifyMsg = "Tarif modifié";
$do->deleteMsg = "Tarif supprimé";

// redirection vers la comptabilite dans le cas de la creation d'un nouveau tarif dans la consult
if(isset($_POST["_tab"])){
  $do->redirect = "m=dPcabinet&tab=".$_POST["_tab"];
}
$do->doIt();

?>