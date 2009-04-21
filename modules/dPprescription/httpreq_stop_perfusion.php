<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $AppUI, $can, $m;

$perfusion_id = mbGetValueFromGet("perfusion_id");

// Chargement de la perfusion
$perfusion = new CPerfusion();
$perfusion->load($perfusion_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("perfusion", $perfusion);
$smarty->display("inc_vw_stop_perf.tpl");

?>