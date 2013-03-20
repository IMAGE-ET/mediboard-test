<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
 
/**
 * CCDAIVL_INT class
 * Choix entre une séquence(low(1.1), [width(0.1)|high(0.1)]), element high(1.1), séquence(width(1.1), high(0.1)),
 * séquence(center(1.1), width(0.1))
 */
class CCDAIVL_INT extends CCDASXCM_INT {

  private $propsHigh = "CCDAIVXB_INT xml|element max|1";
  private $propsWidth = "CCDAINT xml|element max|1";
  private $propsLow = "CCDAIVXB_INT xml|element max|1";
  private $propsCenter = "CCDAINT xml|element max|1";
  private $_order = null;

  /**
   * The low limit of the interval.
   *
   * @var CCDAIVXB_INT
   */
  public $low;

  /**
   * The difference between high and low boundary. The
   * purpose of distinguishing a width property is to
   * handle all cases of incomplete information
   * symmetrically. In any interval representation only
   * two of the three properties high, low, and width need
   * to be stated and the third can be derived.
   *
   * @var CCDAINT
   */
  public $width;

  /**
   * The high limit of the interval.
   *
   * @var CCDAIVXB_INT
   */
  public $high;

  /**
   * The arithmetic mean of the interval (low plus high
   * divided by 2). The purpose of distinguishing the center
   * as a semantic property is for conversions of intervals
   * from and to point values.
   *
   * @var CCDAINT
   */
  public $center;

  /**
   * Setter center
   *
   * @param \CCDAINT $center \CCDAINT
   *
   * @return void
   */
  public function setCenter($center) {
    $this->setOrder("center");
    $this->center = $center;
  }

  /**
   * Getter center
   *
   * @return \CCDAINT
   */
  public function getCenter() {
    return $this->center;
  }

  /**
   * Setter High
   *
   * @param \CCDAIVXB_INT $high \CCDAIVXB_INT
   *
   * @return void
   */
  public function setHigh($high) {
    $this->setOrder("high");
    $this->high = $high;
  }

  /**
   * Getter high
   *
   * @return \CCDAIVXB_INT
   */
  public function getHigh() {
    return $this->high;
  }

  /**
   * Setter low
   *
   * @param \CCDAIVXB_INT $low \CCDAIVXB_INT
   *
   * @return void
   */
  public function setLow($low) {
    $this->setOrder("low");
    $this->low = $low;
  }

  /**
   * Getter low
   *
   * @return \CCDAIVXB_INT
   */
  public function getLow() {
    return $this->low;
  }

  /**
   * Setter width
   *
   * @param \CCDAINT $width \CCDAINT
   *
   * @return void
   */
  public function setWidth($width) {
    $this->setOrder("width");
    $this->width = $width;
  }

  /**
   * Getter width
   *
   * @return \CCDAINT
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   * Affecte la séquence choisi
   *
   * @param String $nameVar String
   *
   * @return void
   */
  function setOrder($nameVar) {
    if (empty($this->_order)||empty($nameVar)) {
      $this->_order = $nameVar;
    }
  }

  /**
	 * Get the properties of our class as strings
	 *
	 * @return array
	 */
  function getProps() {
    $props = parent::getProps();
    switch ($this->_order) {
      case "low":
        $props["low"] = $this->propsLow;
        $props["width"] = $this->propsWidth;
        $props["high"] = $this->propsHigh;
        break;
      case "high":
        $props["high"] = $this->propsHigh;
        break;
      case "width":
        $props["width"] = $this->propsWidth;
        $props["high"] = $this->propsHigh;
        break;
      case "center":
        $props["center"] = $this->propsCenter;
        $props["width"] = $this->propsWidth;
        break;
      default:
        $props["low"] = $this->propsLow;
        $props["width"] = $this->propsWidth;
        $props["high"] = $this->propsHigh;
        $props["center"] = $this->propsCenter;
    }

    return $props;
  }

  /**
   * Fonction permettant de tester la classe
   *
   * @return array
   */
  function test() {
    $tabTest = parent::test();

    /**
     * Test avec element low incorrecte, séquence low
     */

    $xbts = new CCDAIVXB_INT();
    $bl = new CCDA_base_bl();
    $bl->setData("TESTTEST");
    $xbts->setInclusive($bl);
    $this->setLow($xbts);
    $tabTest[] = $this->sample("Test avec un low incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element low correcte, séquence low
     */

    $bl->setData("true");
    $xbts->setInclusive($bl);
    $this->setLow($xbts);
    $tabTest[] = $this->sample("Test avec un low correcte", "Document valide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element high incorrecte, séquence low
     */

    $hi = new CCDAIVXB_INT();
    $bl = new CCDA_base_bl();
    $bl->setData("TESTTEST");
    $hi->setInclusive($bl);
    $this->setHigh($hi);
    $tabTest[] = $this->sample("Test avec un high incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element high correcte, séquence low
     */

    $bl->setData("true");
    $hi->setInclusive($bl);
    $this->setHigh($hi);
    $tabTest[] = $this->sample("Test avec un high correcte", "Document valide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element width incorrecte, séquence low incorrecte
     */

    $wid = new CCDAINT();
    $int = new CCDA_base_int();
    $int->setData("10.25");
    $wid->setValue($int);
    $this->setWidth($wid);
    $tabTest[] = $this->sample("Test avec un width incorrecte, séquence incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element width correcte, séquence low incorrecte
     */

    $int->setData("10");
    $wid->setValue($int);
    $this->setWidth($wid);
    $tabTest[] = $this->sample("Test avec un width correcte, séquence incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element high incorrecte
     */
    $this->setOrder(null);
    $this->low = null;
    $this->width = null;
    $this->center = null;
    $hi = new CCDAIVXB_INT();
    $bl = new CCDA_base_bl();
    $bl->setData("TESTTEST");
    $hi->setInclusive($bl);
    $this->setHigh($hi);
    $tabTest[] = $this->sample("Test avec un high incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element high correcte
     */

    $bl->setData("true");
    $hi->setInclusive($bl);
    $this->setHigh($hi);
    $tabTest[] = $this->sample("Test avec un high correcte", "Document valide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element width incorrecte, séquence width
     */

    $this->high = null;
    $this->setOrder(null);
    $wid = new CCDAINT();
    $int = new CCDA_base_int();
    $int->setData("10.25");
    $wid->setValue($int);
    $this->setWidth($wid);
    $tabTest[] = $this->sample("Test avec un width incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element width correcte, séquence width
     */

    $int->setData("10");
    $wid->setValue($int);
    $this->setWidth($wid);
    $tabTest[] = $this->sample("Test avec un width correcte", "Document valide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element high incorrecte, séquence width
     */

    $hi2 = new CCDAIVXB_INT();
    $bl = new CCDA_base_bl();
    $bl->setData("TESTTEST");
    $hi2->setInclusive($bl);
    $this->setHigh($hi2);
    $tabTest[] = $this->sample("Test avec un high incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element high correcte, séquence width
     */

    $bl->setData("true");
    $hi2->setInclusive($bl);
    $this->setHigh($hi2);
    $tabTest[] = $this->sample("Test avec un high correcte", "Document valide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element center incorrecte, séquence center
     */
    $this->setOrder(null);
    $this->width = null;
    $this->high = null;
    $cen = new CCDAINT();
    $intC = new CCDA_base_int();
    $intC->setData("10.25");
    $cen->setValue($intC);
    $this->setCenter($cen);
    $tabTest[] = $this->sample("Test avec un center incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element center correcte, séquence center
     */

    $intC->setData("10");
    $cen->setValue($intC);
    $this->setCenter($cen);
    $tabTest[] = $this->sample("Test avec un center correcte", "Document valide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element width incorrecte, séquence center
     */

    $cenW = new CCDAINT();
    $intCW = new CCDA_base_int();
    $intCW->setData("10.25");
    $cenW->setValue($intCW);
    $this->setCenter($cenW);
    $tabTest[] = $this->sample("Test avec un width incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------------*/

    /**
     * Test avec element width correcte, séquence center
     */

    $intCW->setData("10");
    $cenW->setValue($intCW);
    $this->setCenter($cenW);
    $tabTest[] = $this->sample("Test avec un width correcte", "Document valide");

    /*-------------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
