<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediuser
* @version $Revision$
* @author Fabien Ménager
*/

$hexa_values = array('00', '33', '66', '99', 'CC', 'FF');
$range = range(0, count($hexa_values)-1);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hex",   $hexa_values);
$smarty->assign("range", $range);
$smarty->assign("color", mbGetValueFromGet("color"));

$smarty->display("color_selector.tpl");

?>