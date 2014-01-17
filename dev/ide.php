<?php
/**
 * IDE call from Web GUI
 *
 * @package    Mediboard
 * @subpackage Dev
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// CLI or die
PHP_SAPI === "cli" or die;

if (!isset($argv[1])) {
  return;
}

$project = dirname(dirname(__FILE__));

require_once "$project/includes/config_all.php";

$ide_path = $dPconfig["dPdeveloppement"]["ide_path"];

if (!$ide_path) {
  return;
}

list($prefix, $file, $line) = explode(":", urldecode($argv[1]));

$file     = escapeshellcmd("$project\\$file");
$project  = escapeshellarg($project);
$ide_path = escapeshellarg($ide_path);

$ides = array(
  "notepad++"    => '%1$s -n%3$d %2$s',
  "PhpStorm"     => '%1$s %4$s --line %3$d %2$s',
  "sublime_text" => '%1$s %2$s:%3$d',
);

$cmd = null;
foreach ($ides as $ide => $pattern) {
  if (stripos($ide_path, $ide) !== false) {
    $cmd = sprintf($pattern, $ide_path, $file, $line, $project);
    break;
  }
}

if (!$cmd) {
  $cmd = sprintf("%s %s", $ide_path, $file);
}

exec($cmd);
