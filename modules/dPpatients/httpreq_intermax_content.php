<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can;

$can->needsRead();

$intermax = CValue::postOrSessionAbs("intermax", array());

$fonction = @$intermax["FONCTION"]["NOM"];

$AppUI->stepAjax("Fonction Intermax '$fonction' reçue", UI_MSG_OK);
$AppUI->callbackAjax("Intermax.handleResult", $fonction);

CApp::rip();

?>