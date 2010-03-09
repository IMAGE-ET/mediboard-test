<?php /* $Id: do_rpu_aed.php 6473 2009-06-24 15:18:19Z lryo $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$bilan = new CBilanSSR;
if ($bilan->sejour_id = CValue::post("sejour_id")) {
	if ($bilan->loadMatchingObject()) {
		$_POST["_id"] = $bilan->_id;
	}
}

$do = new CDoObjectAddEdit("CBilanSSR");
$do->doIt();

?>