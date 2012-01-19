<?php /* $Id: ajax_item_prestation_autocomplete.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$keywords = CValue::get("keywords");

$item_prestation = new CItemPrestation;

$ljoin = array();
$ljoin
?>