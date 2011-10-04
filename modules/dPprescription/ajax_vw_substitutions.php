<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$line_id = CValue::get("line_id");
$can_select_equivalent = CValue::get("can_select_equivalent");
$mode_pharma = CValue::get("mode_pharma");
$line = new CPrescriptionLineMedicament();
$line->load($line_id);

// Chargement de la ligne original
$line->loadRefSubstituteFor();
$line->_ref_substitute_for->loadBackRefs("substitution");

$smarty = new CSmartyDP();
$smarty->assign("line", $line);
$smarty->assign("can_select_equivalent", $can_select_equivalent);
$smarty->assign("mode_protocole", "0");
$smarty->assign("mode_pharma", $mode_pharma);
$smarty->assign("prescription", $line->_ref_prescription);
$smarty->display("inc_vw_substitutions.tpl");
?>