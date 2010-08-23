<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkEdit();

$societe_id = CValue::getOrSession('societe_id');
$suppliers = CValue::getOrSession('suppliers', 1);
$manufacturers = CValue::getOrSession('manufacturers', 1);
$inactive = CValue::getOrSession('inactive', 1);

// Loads the expected Societe
$societe = new CSociete();
$societe->load($societe_id);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('societe',       $societe);
$smarty->assign('suppliers',     $suppliers);
$smarty->assign('manufacturers', $manufacturers);
$smarty->assign('inactive',      $inactive);

$smarty->display('vw_idx_societe.tpl');

?>