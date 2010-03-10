<?php /* $Id: index.php 7320 2009-11-14 22:42:28Z lryo $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_aed_sejour_ssr"      , TAB_READ);
$module->registerTab("vw_idx_plateau"         , TAB_READ);
$module->registerTab("vw_idx_repartition"     , TAB_READ);
$module->registerTab("vw_cdarr"               , TAB_READ);
$module->registerTab("edit_codes_intervenants", TAB_ADMIN);

?>