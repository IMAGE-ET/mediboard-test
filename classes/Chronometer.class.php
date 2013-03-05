<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */
 
/**
 * Time tracking utility class
 */
class Chronometer {
  public $total = 0;
  public $step  = 0;
  public $maxStep = 0;
  public $avgStep = 0;
  public $nbSteps = 0;
  public $main = false;
  public $latestStep = 0;

  public $report = array();
  
  /**
   * Starts the chronometer
   * 
   * @return void
   */
  function start() {
    $this->nbSteps++;
    $this->step = microtime(true);
  }
  
  /**
   * Pauses the chronometer, saving a step
   * 
   * @param string $key The key of the step
   * 
   * @return void
   */
  function stop($key = "") {
    if ($this->step === 0) {
      trigger_error("Chrono stopped without starting", E_USER_WARNING);
      return;
    }
    
    $time = microtime(true);
    $this->step =  $time - $this->step;
    $this->total += $this->step;
    $this->maxStep = max($this->maxStep, $this->step);
    $this->avgStep = $this->total / $this->nbSteps;

    if ($key) {
      if (!array_key_exists($key, $this->report)) {
        $this->report[$key] = new self;
      }
      
      $report =& $this->report[$key];
      $report->nbSteps++;
      $report->step = $this->step;
      $report->total += $report->step;
      $report->maxStep = max($report->maxStep, $report->step);
      $report->avgStep = $report->total/$report->nbSteps;
    }
    
    $this->latestStep = $this->step;
    $this->step = 0; 
  }
}
