<?php /* $Id: index.php 7320 2009-11-14 22:42:28Z lryo $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 7320 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_plateau", TAB_READ);

?>