<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 11749 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::get("sejour_id");

$transmission = new CTransmissionMedicale;
$where = array();
$where["sejour_id"] = " = '$sejour_id'";

$nb_trans = $transmission->countList($where);

echo $nb_trans;
?>