<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_facture"      , TAB_READ);
$module->registerTab("vw_idx_factureitem"  , TAB_READ);

?>