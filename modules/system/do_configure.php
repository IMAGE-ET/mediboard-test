<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $mbpath, $AppUI, $dPconfig, $can, $m, $tab;

$can->needsAdmin();

$mbpath = "";

unset($_POST["m"]);
unset($_POST["dosql"]);
$mbConfig = new CMbConfig;
$mbConfig->update($_POST);
$mbConfig->load();

$dPconfig = $mbConfig->values;
?>