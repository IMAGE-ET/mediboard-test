<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CExamNyha", "examnyha_id");
$do->createMsg = "Classification NYHA créé";
$do->modifyMsg = "Classification NYHA modifié";
$do->deleteMsg = "Classification NYHA supprimé";
$do->redirect = null;
$do->doIt();
?>