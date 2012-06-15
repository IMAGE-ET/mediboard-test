<?php /* $Id: do_box_urgence_aed.php $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$do = new CDoObjectAddEdit("CBoxUrgence", "box_urgences_id");
$do->doIt();

?>