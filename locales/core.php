<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage locales
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $shm;
$root_dir = CAppUI::conf("root_dir");
$locale = $AppUI->user_prefs["LOCALE"];
$shared_name = "locales-$locale";

// Load from shared memory if possible
if (null == $locales = $shm->get($shared_name)) {
  foreach (glob("$root_dir/locales/$locale/*.php") as $file) {
    require_once($file);
  }
  
  foreach (glob("$root_dir/modules/*/locales/$locale.php") as $file) {
    require_once($file);
  }

  $shm->put($shared_name, $locales);
}

// Encoding definition
require("$root_dir/locales/$locale/meta.php");
