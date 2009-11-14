<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_incident"            , TAB_READ);
$module->registerTab("vw_incidentvalid"       , TAB_READ);
$module->registerTab("vw_edit_ei"             , TAB_ADMIN);
$module->registerTab("vw_stats"               , TAB_ADMIN);
$module->registerTab("vw_procedures"          , TAB_READ);
$module->registerTab("vw_procencours"         , TAB_EDIT);
$module->registerTab("vw_procvalid"           , TAB_ADMIN);
$module->registerTab("vw_edit_classification" , TAB_ADMIN);
$module->registerTab("vw_modeles"             , TAB_EDIT);

?>