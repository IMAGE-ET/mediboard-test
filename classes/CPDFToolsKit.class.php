<?php

/**
 * $Id$
 *  
 * @category DMP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe for pdftk
 */
class CPDFToolsKit {

  public $path_file_source;
  public $fdf;
  public $exec = "pdftk";

  /**
   * Construct
   *
   * @param String $path File source path
   * @param String $fdf  File fdf path
   */
  function __construct($path, $fdf) {
    $this->path_file_source = $path;
    $this->fdf = $fdf;
  }

  /**
   * Fills the single input PDFs form fields with the data from an FDF file
   * XFDF file or stdin
   *
   * @param String $output  Path File of result
   * @param String $options Options
   *
   * @return bool|string
   */
  function fillForm($output = null, $options = null){

    $command = $this->createCommand("fill_form", $output, $options);

    $processorInstance = proc_open($command, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
    $processorResult = stream_get_contents($pipes[1]);
    $processorErrors = stream_get_contents($pipes[2]);
    proc_close($processorInstance);

    if ($processorErrors) {
      return false;
    }

    if (empty($processorResult)) {
      return true;
    }

    return $processorResult;
  }

  /**
   * Create the command lign
   *
   * @param String $command Command to execute
   * @param String $output  File path of output
   * @param String $options Options
   *
   * @return string
   */
  private function createCommand($command, $output = null, $options = null) {
    $cmd = "$this->exec $this->path_file_source $command $this->fdf";
    $cmd .= " output";
    if ($output) {
      $cmd .= " $output";
    }
    else {
      $cmd .= " -";
    }

    if ($options) {
      $cmd .= " $options";
    }

    return escapeshellcmd($cmd);
  }
}