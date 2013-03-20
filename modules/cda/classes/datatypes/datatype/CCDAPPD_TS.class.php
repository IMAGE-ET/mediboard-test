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
 * CCDAPPD_TS class
 */
class CCDAPPD_TS extends CCDATS {

  /**
   * The primary measure of variance/uncertainty of the
   * value (the square root of the sum of the squares of
   * the differences between all data points and the mean).
   * The standard deviation is used to normalize the data
   * for computing the distribution function. Applications
   * that cannot deal with probability distributions can
   * still get an idea about the confidence level by looking
   * at the standard deviation.
   *
   * @var CCDAPQ
   */
  public $standardDeviation;

  /**
   * A code specifying the type of probability distribution.
   * Possible values are as shown in the attached table.
   * The NULL value (unknown) for the type code indicates
   * that the probability distribution type is unknown. In
   * that case, the standard deviation has the meaning of an
   * informal guess.
   *
   * @var CCDAProbabilityDistributionType
   */
  public $distributionType;

  /**
   * retourne le nom du type CDA
   *
   * @return string
   */
  function getNameClass() {
    $name = get_class($this);
    $name = substr($name, 4);

    return $name;
  }

  /**
   * Setter distributionType
   *
   * @param \CCDAProbabilityDistributionType $distributionType \CCDAProbabilityDistributionType
   *
   * @return void
   */
  public function setDistributionType($distributionType) {
    $this->distributionType = $distributionType;
  }

  /**
   * Getter DistributionType
   *
   * @return \CCDAProbabilityDistributionType
   */
  public function getDistributionType() {
    return $this->distributionType;
  }

  /**
   * Setter standardDeviation
   *
   * @param \CCDAPQ $standardDeviation \CCDAPQ
   *
   * @return void
   */
  public function setStandardDeviation($standardDeviation) {
    $this->standardDeviation = $standardDeviation;
  }

  /**
   * Getter standardDeviation
   *
   * @return \CCDAPQ
   */
  public function getStandardDeviation() {
    return $this->standardDeviation;
  }

  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["standardDeviation"] = "CCDAPQ xml|element max|1";
    $props["distributionType"] = "CCDAProbabilityDistributionType xml|attribute";
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
     * Test avec un distributionType incorrecte
     */

    $prob = new CCDAProbabilityDistributionType();
    $prob->setData("TESTTEST");
    $this->setDistributionType($prob);
    $tabTest[] = $this->sample("Test avec un distributionType incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un distributionType correcte
     */

    $prob->setData("F");
    $this->setDistributionType($prob);
    $tabTest[] = $this->sample("Test avec un distributionType correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un standardDeviation incorrecte
     */

    $pq = new CCDAPQ();
    $real = new CCDA_base_real();
    $real->setData("test");
    $pq->setValue($real);
    $this->setStandardDeviation($pq);
    $tabTest[] = $this->sample("Test avec un standardDeviation incorrecte", "Document invalide");

    /*-------------------------------------------------------------------------------------*/

    /**
     * Test avec un standardDeviation correcte
     */

    $real->setData("10.25");
    $pq->setValue($real);
    $this->setStandardDeviation($pq);;
    $tabTest[] = $this->sample("Test avec un standardDeviation correcte", "Document valide");

    /*-------------------------------------------------------------------------------------*/

    return $tabTest;
  }
}
