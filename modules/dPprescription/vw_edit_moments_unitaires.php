<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Chargement des moments unitaires
$moments = CMomentUnitaire::loadAllMoments();

$hours = range(0,23);
foreach($hours as &$hour){
	$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("moments", $moments);
$smarty->assign("hours", $hours);
$smarty->display("vw_edit_moments_unitaires.tpl");

?>