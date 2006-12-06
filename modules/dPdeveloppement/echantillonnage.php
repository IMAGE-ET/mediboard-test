<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
    $AppUI->redirect("m=system&a=access_denied");
}

$etablissements = CMediusers::loadEtablissements(PERM_EDIT);

$smarty = new CSmartyDP(1);

$smarty->assign("etablissements", $etablissements);
$smarty->assign("today"         , date("Y-m-d"));

$smarty->display("echantillonnage.tpl");
?>