<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// Initialisation de variables

$selAdmis = mbGetValueFromGetOrSession("selAdmis", "0");
$selSaisis = mbGetValueFromGetOrSession("selSaisis", "0");
$selTri = mbGetValueFromGetOrSession("selTri", "nom");
$date = mbGetValueFromGetOrSession("date", mbDate());

// Création du template
$smarty = new CSmartyDP();


$smarty->assign("date"     , $date);
$smarty->assign("selAdmis" , $selAdmis);
$smarty->assign("selSaisis", $selSaisis);
$smarty->assign("selTri"   , $selTri);

$smarty->display("vw_idx_admission.tpl");

?>