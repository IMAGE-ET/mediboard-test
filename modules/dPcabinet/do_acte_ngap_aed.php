<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CActeNGAP", "acte_ngap_id");
$do->createMsg = "Acte NGAP créé";
$do->modifyMsg = "Acte NGAP modifié";
$do->deleteMsg = "Acte NGAP supprimé";
$do->doIt();

?>