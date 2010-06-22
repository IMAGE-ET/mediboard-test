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
  foreach (CAppUI::getLocaleFilesPaths($locale) as $_path) {
  	require_once($_path);
  }
  $locales = array_filter($locales, "stringNotEmpty");
  foreach($locales as &$_locale) {
    $_locale = CMbString::unslash($_locale);
  }
  SHM::put($shared_name, $locales);
}

// Encoding definition
require("$root_dir/locales/$locale/meta.php");
