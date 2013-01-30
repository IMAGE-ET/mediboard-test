<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_factures_consult", TAB_READ);
$module->registerTab("vw_factures_sejour" , TAB_READ);
?>