<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPfiles
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$file_name = $_GET["checksum"];

// PHP interprête mal les données reçues en POST sur cette requête
// Récupération des données directement sur php://input
$content = file_get_contents("php://input");

file_put_contents("../../tmp/".$file_name, $content);

?>