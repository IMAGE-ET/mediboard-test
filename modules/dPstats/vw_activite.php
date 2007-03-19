<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$debutact      = mbGetValueFromGetOrSession("debutact", mbDate());
$finact        = mbGetValueFromGetOrSession("finact", mbDate());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("debutact", $debutact);
$smarty->assign("finact"  , $finact);

$smarty->display("vw_activite.tpl");

?>