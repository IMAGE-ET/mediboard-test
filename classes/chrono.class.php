<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
// Chronometer
class Chronometer {
  var $total = 0;
  var $step  = 0;
  var $maxStep = 0;
  var $avgStep = 0;
  var $nbSteps = 0;
  
  var $report = array();
  
  function start() {
    $this->nbSteps++;
    $this->step = microtime(true);
  }
  
  function stop($key = "") {
    $this->step = microtime(true) - $this->step;
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
  }
}
?>