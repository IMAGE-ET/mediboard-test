<?php /* $Id: ajax_edit_affectations.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$affectation_id = CValue::get("affectation_id");
$lit_id         = CValue::get("lit_id");

$affectation = new CAffectation;
$affectation->load($affectation_id);

if (!$affectation->_id) {
  $affectation->lit_id = $lit_id;
}

$smarty = new CSmartyDP;

$smarty->assign("affectation", $affectation);

$smarty->display("inc_edit_affectation.tpl");
?>