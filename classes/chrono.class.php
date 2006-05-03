<?php /* CLASSES $Id: chrono.class.php,v 1.2 2006/03/08 18:08:52 mytto Exp $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision: 1.2 $
 */
// Chronometer
class Chronometer {
  var $total = 0;
  var $step  = 0;
  var $maxStep = 0;
  var $avgStep = 0;
  var $nbSteps = 0;
    
  function Chronometer() {
  }
  
  function microtimeFloat() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
  
  function start() {
    $this->nbSteps++;
    $this->step = $this->microtimeFloat();
  }
  
  function stop() {
    $this->step = $this->microtimeFloat() - $this->step;
    $this->total += $this->step;
    
    if ($this->step > $this->maxStep) {
			$this->maxStep = $this->step;
		}
    
    $this->avgStep = $this->total/$this->nbSteps;
  }
}
?>