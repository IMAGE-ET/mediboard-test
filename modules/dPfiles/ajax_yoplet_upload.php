<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPfiles
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$file_name = $_POST["checksum"];
file_put_contents("tmp/".$file_name, file_get_contents($_FILES["file"]["tmp_name"]));

?>