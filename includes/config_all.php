<?php 
/**
 * Configuration loader
 *  
 * @category Config
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$ 
 * @link     http://www.mediboard.org
 */

// Distribution configuration
require __DIR__."/config_dist.php";

// Local configuration, if it exists (does not exist when installing)
if (file_exists(__DIR__."/config.php")) {
  include __DIR__."/config.php";
}

// Modules configuration 
// !!!! doesn't work when the code is here and not in config_dist.php, don't know why
//$config_files = glob(__DIR__."/../modules/*/config.php");
//foreach ($config_files as $file) {
//  require $file;
//}

// Overload configuration (for master/slave)
if (is_file(__DIR__."/config_overload.php")) {
  include __DIR__."/config_overload.php";
}
