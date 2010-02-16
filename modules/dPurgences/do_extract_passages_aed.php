<?php /* $Id: do_rpu_aed.php 6473 2009-06-24 15:18:19Z lryo $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 6473 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$do = new CDoObjectAddEdit("CExtractPassages", "extract_passages_id");
$do->doIt();

?>