<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_planning_week"     , null, TAB_EDIT);
$module->registerTab("vw_edit_planning"     , null, TAB_READ);
$module->registerTab("vw_edit_interventions", null, TAB_EDIT);
$module->registerTab("vw_suivi_salles"      , null, TAB_EDIT);
$module->registerTab("vw_urgences"          , null, TAB_EDIT);
$module->registerTab("vw_idx_materiel"      , null, TAB_EDIT);
$module->registerTab("vw_idx_blocs"         , null, TAB_EDIT);
$module->registerTab("vw_idx_salles"        , null, TAB_EDIT);
$module->registerTab("print_planning"       , null, TAB_READ);

?>