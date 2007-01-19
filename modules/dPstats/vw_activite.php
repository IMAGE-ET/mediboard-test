<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$debutact      = mbGetValueFromGetOrSession("debutact", mbDate());
$finact        = mbGetValueFromGetOrSession("finact", mbDate());

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("debutact", $debutact);
$smarty->assign("finact"  , $finact);

$smarty->display("vw_activite.tpl");

?>