<?php

/**
 * Clear Op code cache entries and user cache entries specific to the current Mediboard instance
 *
 * $Id$
 *
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

// Mediboard root dir
$root_dir = realpath(__DIR__."/../../../");

// Check clear cache flag
$flag_path = "$root_dir/tmp/clearcache.flag";
if (!file_exists($flag_path)) {
  echo "Flag path doesn't exist, exiting\n";
  exit(0);
}

if (filemtime($flag_path)+10 < time()) {
  echo "Flag path too old, exiting\n";
  exit(0);
}

// Remove flag file
@unlink($flag_path);

// Clear op code cache
if (function_exists("apc_delete_file")) {
  $cache_info = apc_cache_info("system");

  $prefix = $root_dir;

  // Clear opcode cache entries
  $entries = array();
  foreach ($cache_info["cache_list"] as $_cache_entry) {
    $_file_entry = $_cache_entry["filename"];

    if (strpos($_file_entry, $root_dir) === 0) {
      $entries[] = $_file_entry;
    }
  }

  apc_delete_file($entries);

  echo count($entries)." opcode cache entries removed\n";
}

// Clear user cache
if (function_exists("apc_delete")) {
  $cache_info = apc_cache_info("user");

  $prefix = preg_replace("/[^\w]+/", "_", $root_dir);

  $count = 0;

  // Clear user cache entries
  foreach ($cache_info["cache_list"] as $_cache_entry) {
    $_cache_entry = $_cache_entry["info"];

    if (strpos($_cache_entry, $prefix) === 0) {
      $count++;
      apc_delete($_cache_entry);
    }
  }

  echo "$count user cache entries removed\n";
}
