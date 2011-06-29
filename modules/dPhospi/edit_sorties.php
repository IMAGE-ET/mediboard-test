<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$vue  = CValue::getOrSession("vue", 0);
$date = CValue::getOrSession("date", mbDate());
$sorties = array("comp" => array(), "ambu" => array());

$smarty = new CSmartyDP;
$smarty->assign("sorties", $sorties);
$smarty->assign("vue", $vue);
$smarty->assign("date", $date);

$smarty->display("edit_sorties.tpl");

?>