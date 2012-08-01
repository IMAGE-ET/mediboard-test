<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

// CLI or die
PHP_SAPI === "cli" or die;

$root_dir = dirname(__FILE__)."/../../..";
require "$root_dir/modules/hl7/classes/CMLLPServer.class.php";

$tmp_dir = CMLLPServer::getTmpDir();

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
