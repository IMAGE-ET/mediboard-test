<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_planning_week"     , TAB_EDIT);
$module->registerTab("vw_edit_planning"     , TAB_READ);
$module->registerTab("vw_edit_interventions", TAB_EDIT);
$module->registerTab("vw_suivi_salles"      , TAB_EDIT);
$module->registerTab("vw_urgences"          , TAB_EDIT);
$module->registerTab("vw_idx_materiel"      , TAB_EDIT);
$module->registerTab("vw_idx_blocs"         , TAB_EDIT);
$module->registerTab("vw_idx_salles"        , TAB_EDIT);
$module->registerTab("print_planning"       , TAB_READ);

?>