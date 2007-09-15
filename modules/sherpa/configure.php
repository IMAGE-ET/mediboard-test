<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision: $
 * @author Sherpa
 */

global $can;

$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("spClasses", array("CSpMalade", "CSpSejMed"));
$smarty->display("configure.tpl");

?>