<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$attente = CValue::get("attente");
$rpu_id  = CValue::get("rpu_id");

// Chargement du rpu
$rpu = new CRPU();
$rpu->load($rpu_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rpu", $rpu);
if (!$attente) {
	$smarty->display("inc_vw_rpu_attente.tpl");
} else {
	$smarty->assign("debut", CValue::get("debut"));
	$smarty->assign("fin", CValue::get("fin"));
	$smarty->display("inc_vw_fin_attente.tpl");
}

?>