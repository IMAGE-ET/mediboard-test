<?php /* $Id: print_rapport.php 2321 2007-07-19 08:14:45Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 2321 $
 * @author Romain Ollivier
 */

global $can;
$can->needsEdit();

// Création du template
$smarty = new CSmartyDP();


$smarty->display("vw_identifiants.tpl");
?>