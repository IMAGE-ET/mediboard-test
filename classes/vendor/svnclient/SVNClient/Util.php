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

use Symfony\Component\Process\ProcessBuilder;

class Util {

  /**
   * Execute an SVN command
   *
   * @param string $cmd       Command name (update, info, etc)
   * @param array  $arguments An array of argument (path, url, etc)
   * @param array  $options   An array of options
   * @param string $path      Working directory
   *
   * @return string
   * @throws \Exception
   */
  public static function exec($cmd, $arguments = array(), $options = array(), $path = null) {
    if (!is_array($arguments)) {
      $arguments = array($arguments);
    }

    $new_options = array();
    foreach ($options as $key => $value) {
      $new_options[] = preg_replace("/[^-\w]/", "", $key);

      if ($value !== true) {
        $new_options[] = $value;
      }
    }

    $arguments = array_merge(array("svn", $cmd), $arguments, $new_options);

    $builder = new ProcessBuilder($arguments);
    $builder->setWorkingDirectory($path);

    $process = $builder->getProcess();
    $process->run();

    if (!$process->isSuccessful()) {
      throw new Exception($process->getErrorOutput());
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