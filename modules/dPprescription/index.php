<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_protocole"           , TAB_EDIT);
$module->registerTab("vw_edit_pack_protocole"      , TAB_EDIT);
$module->registerTab("vw_edit_liaison_admission"   , TAB_EDIT);
$module->registerTab("vw_edit_category"            , TAB_ADMIN);
$module->registerTab("vw_edit_categories_group"    , TAB_ADMIN);
$module->registerTab("vw_edit_associations_moments", TAB_ADMIN);
$module->registerTab("vw_edit_moments_unitaires"   , TAB_ADMIN);
$module->registerTab("vw_edit_config_service"      , TAB_ADMIN);
?>