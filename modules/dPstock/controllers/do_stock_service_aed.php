<?php /* $Id: do_stock_service_aed.php 6067 2009-04-14 08:04:15Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 6067 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$do = new CDoObjectAddEdit('CProductStockService', 'stock_id');
$do->doIt();

?>