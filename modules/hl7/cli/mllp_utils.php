<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// CLI or die
PHP_SAPI === "cli" or die;

global $root_dir, $tmp_dir;

$root_dir = dirname(__FILE__)."/../../..";
require "$root_dir/classes/CMbPath.class.php";
require "$root_dir/lib/phpsocket/SocketClient.php";
require "$root_dir/modules/hl7/classes/CMLLPSocketHandler.class.php";

$tmp_dir = "$root_dir/tmp/socket_server";
CMbPath::forceDir($tmp_dir);

function outln($str){
  $stdout = fopen("php://stdout", "w");
  fwrite($stdout, $str.PHP_EOL);
}