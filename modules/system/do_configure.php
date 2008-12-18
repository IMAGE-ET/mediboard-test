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
CMbArray::extract($_POST, "m");
CMbArray::extract($_POST, "dosql");
CMbArray::extract($_POST, "suppressHeaders");
$ajax = CMbArray::extract($_POST, "ajax");

$mbConfig = new CMbConfig;
$mbConfig->update($_POST);
$mbConfig->load();
$AppUI->setMsg("Configuration modifiée");

$dPconfig = $mbConfig->values;

// Cas Ajax
if ($ajax) {
  echo $AppUI->getMsg();
  CApp::rip();
}

?>