<?php

/**
 * Debug
 *
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

global $dPconfig;

define("DEBUG_PATH", $dPconfig["root_dir"]."/tmp/mb-debug.html");

/**
 * Class CMbDebug
 * used to manage debug log
 */
class CMbDebug {
  const DEBUG_PATH       = DEBUG_PATH;
  const DEBUG_SIZE_LIMIT = 5242880; // 1024*1024*5

  /**
   * Process the exported data
   *
   * @param string $export         Data
   * @param string $label          Add an optionnal label
   * @param bool   $onlyPlainField Only get DB fields and there values if export is object
   *
   * @return int The size of the data written in the log file
   **/
  static function log($export, $label = null, $onlyPlainField = false) {
    if (!CAppUI::conf("debug")) {
      return null;
    }

    if ($export instanceof CMbObject && $onlyPlainField) {
      $export = $export->getPlainFields();
    }

    $export = print_r($export, true);
    $export = CMbString::htmlSpecialChars($export);
    $time = date("Y-m-d H:i:s");
    $msg = "\n<pre>[$time] $label: $export</pre>";

    return file_put_contents(DEBUG_PATH, $msg, FILE_APPEND);
  }
}