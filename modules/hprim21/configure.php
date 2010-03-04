<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage hprim21
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsAdmin();

$type = "ftp";
$exchange_source = CExchangeSource::get("hprim21", $type);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("type"            , $type);
$smarty->assign("exchange_source" , $exchange_source);
$smarty->display("configure.tpl");

?>