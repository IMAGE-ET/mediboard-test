<?php 
/**
 * Index EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_interop_actors"      , TAB_READ);
$module->registerTab("vw_idx_exchange_data_format", TAB_READ);
$module->registerTab("vw_sources"                 , TAB_READ);
$module->registerTab("vw_routers"                 , TAB_ADMIN);
$module->registerTab("vw_transformations"         , TAB_ADMIN);
$module->registerTab("vw_servers_socket"          , TAB_ADMIN);
$module->registerTab("vw_domains"                 , TAB_ADMIN);
$module->registerTab('vw_tunnel_tools'            , TAB_ADMIN);
$module->registerTab("vw_tools"                   , TAB_ADMIN);
$module->registerTab("vw_stats"                   , TAB_ADMIN);