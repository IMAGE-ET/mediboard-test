<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Thomas Despoix
*/

global $can;

$can->needsRead();

$intermax = CValue::postOrSessionAbs("intermax", array());

$fonction = @$intermax["FONCTION"]["NOM"];

CAppUI::stepAjax("Fonction Intermax '$fonction' reçue", UI_MSG_OK);
CAppUI::callbackAjax("Intermax.handleResult", $fonction);

CApp::rip();

?>