<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

$etablissements = CMediusers::loadEtablissements(PERM_EDIT);

$smarty = new CSmartyDP();

$smarty->assign("etablissements", $etablissements);
$smarty->assign("today"         , date("Y-m-d"));

$smarty->display("echantillonnage.tpl");
?>