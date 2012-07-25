<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

if (CAppUI::conf("dPadmissions use_recuse")) {
  $module->registerTab("vw_sejours_validation", TAB_EDIT);
}
$module->registerTab("vw_idx_admission"         , TAB_READ);
$module->registerTab("vw_idx_sortie"            , TAB_READ);
$module->registerTab("vw_idx_preadmission"      , TAB_READ);
$module->registerTab("vw_idx_permissions"       , TAB_READ);
$module->registerTab("vw_idx_present"           , TAB_READ);
//$module->registerTab("vw_idx_consult"         , TAB_READ);
$module->registerTab("vw_idx_identito_vigilance", TAB_ADMIN);

?>