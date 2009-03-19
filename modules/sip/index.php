<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann  
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_soapclient", null, TAB_READ);
$module->registerTab("vw_idx_cip", null, TAB_READ);

?>