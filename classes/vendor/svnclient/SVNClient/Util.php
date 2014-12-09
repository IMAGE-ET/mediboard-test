<?php
/**
 * Working copy class
 *
 * @package SVNClient
 * @link    https://github.com/PhenX/svnclient
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license MIT License (MIT)
 */

namespace SVNClient;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\RuntimeException;

class Util {

  /**
   * Execute an SVN command
   *
   * @param string  $cmd       Command name (update, info, etc)
   * @param array   $arguments An array of argument (path, url, etc)
   * @param array   $options   An array of options
   * @param string  $path      Working directory
   * @param integer $timeout   Timeout
   *
   * @return string
   * @throws \Exception
   */
  public static function exec($cmd, $arguments = array(), $options = array(), $path = null, $output = false, $timeout = null) {
    if (!is_array($arguments)) {
      $arguments = array($arguments);
    }

    $arguments = array_map("escapeshellarg", $arguments);

    $new_options = array();
    foreach ($options as $key => $value) {
      $new_options[] = preg_replace("/[^-\w]/", "", $key);

      if ($value !== true) {
        $new_options[] = escapeshellarg($value);
      }
    }

    $cmdline = "svn $cmd " . implode(" ", $arguments) . " " . implode(" ", $new_options);

    $process = new Process($cmdline, $path, null, null, $timeout);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new RuntimeException($process->getErrorOutput());
    }

    if ($output) {
      echo $process->getOutput();
      return null;
    }

    return $process->getOutput();
  }

  /**
   * @param $xml
   *
   * @return \SimpleXMLElement[]
   */
  public static function parseXML($xml) {
    $dom = simplexml_load_string($xml);

    foreach ($dom->children() as $child) {
      return $child;
    }
  }
}