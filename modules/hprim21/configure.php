<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage hprim21
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsAdmin();

$hprim21_source = CExchangeSource::get("hprim21", "ftp", true);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hprim21_source" , $hprim21_source);

$smarty->display("configure.tpl");

?>