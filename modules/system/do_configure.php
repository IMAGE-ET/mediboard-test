<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $mbpath, $AppUI, $dPconfig, $canAdmin, $canRead, $canEdit, $m, $tab;

if(!$canAdmin) {
    $AppUI->redirect("m=system&a=access_denied");
}
$mbpath = "";

unset($_POST["m"]);
unset($_POST["dosql"]);

$mbConfig = new CMbConfig;
$mbConfig->update($_POST);
$mbConfig->load();

$dPconfig = $mbConfig->values;
?>