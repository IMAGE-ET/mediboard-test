<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

// RHS concernés
$rhs = new CRHS();
$rhs->load(CValue::get("rhs_id"));

$rhs->loadRefDependances();
$rhs->loadRefSejour();

if(!$rhs->_ref_dependances->_id) {
  $rhs->_ref_dependances->store();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("rhs", $rhs);

$smarty->display("inc_edit_dependances_rhs.tpl");
