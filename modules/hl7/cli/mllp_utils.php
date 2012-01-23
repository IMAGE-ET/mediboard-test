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

$root_dir = dirname(__FILE__)."/../../..";
require "$root_dir/modules/hl7/classes/CMLLPServer.class.php";

$tmp_dir = CMLLPServer::getTmpDir();

function outln($str){
  $stdout = fopen("php://stdout", "w");
  fwrite($stdout, $str.PHP_EOL);
}

