<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien Ménager
*/

global $AppUI, $can, $m;

$can->needsAdmin();

// Création du template
$smarty = new CSmartyDP();

$smarty->display('configure.tpl');
?>