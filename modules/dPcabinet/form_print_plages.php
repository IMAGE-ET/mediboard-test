<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $can, $m;

$can->needsRead();

$deb = mbDate();
$fin = mbDate("+ 0 day");

// Liste des praticiens
$mediusers = new CMediusers();
$listChir = $mediusers->loadPraticiens(PERM_EDIT);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("deb"     , $deb     );
$smarty->assign("fin"     , $fin     );
$smarty->assign("listChir", $listChir);

$smarty->display("form_print_plages.tpl");

?>