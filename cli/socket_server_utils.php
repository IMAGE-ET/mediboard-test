<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage cli
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// CLI or die
PHP_SAPI === "cli" or die;

$root_dir = dirname(__FILE__)."/..";
require "$root_dir/classes/CSocketBasedServer.class.php";


$server_type = "";
$server_class = "";
$class_path = "";
switch ($argv[1]) {
  case "dicom" :
    $server_type = "Dicom";
    $server_class = "CDicomServer";
    $class_path = dirname(__FILE__) . "/../modules/dicom/classes/$server_class.class.php";
    break;
  case "mllp" :
    $server_type = "MLLP";
    $server_class = "CMLLPServer";
    $class_path = dirname(__FILE__) . "/../modules/hl7/classes/$server_class.class.php";
    break;
  default :
    echo "Incorrect server type specified!\n";
    exit(0);
}

require_once $class_path;

$tmp_dir = CSocketBasedServer::getTmpDir();

/**
 * Simple output function
 * 
 * @param string $str The string to output
 * 
 * @return void
 */
function outln($str){
  $stdout = fopen("php://stdout", "w");
  fwrite($stdout, $str.PHP_EOL);
}
