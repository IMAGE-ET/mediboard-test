<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Thomas Despoix
*/

CCanDo::checkAdmin();

$hours = range(0, 23);
$intervals = array("05","10","15","20","30");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("hours"     , $hours);
$smarty->assign("date"      , mbDate());
$smarty->assign("intervals" , $intervals);

$smarty->assign("anesths" , CMediusers::get()->loadAnesthesistes());
$smarty->assign("user"    , CUser::get());

$smarty->display("configure.tpl");
?>