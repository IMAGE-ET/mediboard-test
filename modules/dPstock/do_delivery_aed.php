<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if (isset ($_POST['_code'])) {
	$stock = CProductStockGroup::getFromCode($_POST['_code']);
	if ($stock) {
		$_POST['stock_id'] = $stock->_id;
		$_POST['_code'] = null;
	}
}

$do = new CDoObjectAddEdit('CProductDelivery', 'delivery_id');
$do->doIt();

?>