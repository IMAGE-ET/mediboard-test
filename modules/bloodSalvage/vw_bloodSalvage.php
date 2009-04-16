<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global  $can, $m, $g;
$can->needsRead();
/*
 * Récupération des variables en session et ou issues des formulaires.
 */
$salle        		= mbGetValueFromGetOrSession("salle");
$op           		= mbGetValueFromGetOrSession("op");
$date         		= mbGetValueFromGetOrSession("date", mbDate());


$blood_salvage = new CBloodSalvage();


$selOp = new COperation();

if ($op) {
  $selOp->load($op);
  $selOp->loadRefs();
	$where = array();
	$where["operation_id"] = "='$selOp->_id'";	
	$blood_salvage->loadObject($where);
}

$smarty = new CSmartyDP();

$smarty->assign("blood_salvage", $blood_salvage);	
$smarty->assign("blood_salvage_id", $blood_salvage->_id);	
$smarty->assign("selOp", $selOp);
$smarty->assign("date", $date);

$smarty->display("vw_bloodSalvage.tpl");
?>