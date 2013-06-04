<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Bezier interoplated point generation,
 * Computed from control points data sets, based on Paul Bourke algorithm
 * 
 * @see http://astronomy.swin.edu.au/~pbourke/curves/bezier/
 */
class BezierTD {
  var $datax = array();
  var $datay = array();
  
  function BezierTD($datax, $datay, $attraction_factor = 1) {
    // Adding control point multiple time will raise their attraction power over the curve    
    foreach($datax as $datumx) {
      for ($i = 0; $i < $attraction_factor; $i++) {
        $this->datax[] = $datumx; 
      }
    }
    
    foreach($datay as $datumy) {
      for ($i = 0; $i < $attraction_factor; $i++) {
        $this->datay[] = $datumy; 
      }
    }

  }

  function Get($steps) {
    $datax = array();
    $datay = array();
    for ($i = 0; $i < $steps; $i++) {
      list($datumx, $datumy) = $this->GetPoint((double) $i / (double) $steps);      
      $datax[] = $datumx;
      $datay[] = $datumy;
    }
    
    $datax[] = end($this->datax);
    $datay[] = end($this->datay);
    
    return array($datax, $datay);
  }
  
  function GetPoint($mu) {
    $n = count($this->datax)-1;
    $k = 0;
    $kn = 0;
    $nn = 0;
    $nkn = 0;
    $blend = 0.0;
    $newx = 0.0;
    $newy = 0.0;

    $muk = 1.0;
    $munk = (double) pow(1-$mu,(double) $n);

    for ($k = 0; $k <= $n; $k++) {
      $nn = $n;
      $kn = $k;
      $nkn = $n - $k;
      $blend = $muk * $munk;
      $muk *= $mu;
      $munk /= (1-$mu);
      while ($nn >= 1) {
         $blend *= $nn;
         $nn--;
         if ($kn > 1) {
            $blend /= (double) $kn;
            $kn--;
         }
         if ($nkn > 1) {
            $blend /= (double) $nkn;
            $nkn--;
         }
      }
      $newx += $this->datax[$k] * $blend;
      $newy += $this->datay[$k] * $blend;
   }
   return array($newx, $newy);
  }
}

?>
