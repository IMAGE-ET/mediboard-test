<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

$societe_id = CValue::getOrSession('societe_id');

// Loads the expected Societe
$societe = new CSociete();
$societe->load($societe_id);
$societe->loadRefsBack();

// Loads every reference supplied by this societe
foreach($societe->_ref_product_references as $key => $value) {
  $value->loadRefsFwd();
}

// Loads every product made by this societe
foreach($societe->_ref_products as $key => $value) {
  $value->loadRefsFwd();
}

// Loads the Societes list
$list_societes = $societe->loadList(null, 'name');

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('societe',       $societe);
$smarty->assign('list_societes', $list_societes);

$smarty->display('vw_idx_societe.tpl');

?>