<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_identifiants"  , TAB_EDIT);
$module->registerTab("vw_soap_services" , TAB_EDIT);
$module->registerTab("export_documents" , TAB_EDIT);
$module->registerTab("manage_categories", TAB_EDIT);
$module->registerTab("export_egate"     , TAB_READ);
$module->registerTab("vw_ssr"           , TAB_EDIT);

?>