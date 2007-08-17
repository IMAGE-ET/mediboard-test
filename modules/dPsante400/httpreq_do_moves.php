<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: $
* @author Thomas Despoix
*/

global $AppUI, $can;

$can->needsAdmin();

$mouv = CMouvFactory::create(mbGetValueFromGet("type"));
$marked = mbGetValueFromGet("marked");

switch (mbGetValueFromGet("action")) {
	case "count";
	$count = $mouv->count($marked);
	$AppUI->stepAjax("$count mouvements disponibles", UI_MSG_OK);
	break;
	
	case "purge";
	$count = $mouv->purge($marked);
	$AppUI->stepAjax("$count mouvements supprimés", UI_MSG_OK);
	break;
	
	default:
  $AppUI->stepAjax("Action '$action' non prise en charge", UI_MSG_ERROR);
	break;
}




?>