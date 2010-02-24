<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage locales
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$root_dir = CAppUI::conf("root_dir");
$locale = CAppUI::pref("LOCALE");
$shared_name = "locales-$locale";

// Load from shared memory if possible
if (null == $locales = SHM::get($shared_name)) {
  foreach (glob("$root_dir/locales/$locale/*.php") as $file) {
    require_once($file);
  }
  
  foreach (glob("$root_dir/modules/*/locales/$locale.php") as $file) {
    require_once($file);
  }
  $locales = array_filter($locales, "stringNotEmpty");
  SHM::put($shared_name, $locales);
}

// Encoding definition
require("$root_dir/locales/$locale/meta.php");
