<?php

/**
 * PDQ - ITI-21 - Tests
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CITI21Test
 * PDQ - ITI-21 - Tests
 */
class CITI21Test extends CIHETestCase {
  /**
   * Test Q22 - Find Candidates
   *
   * @param CCnStep $step Step
   *
   * @throws CMbException
   *
   * @return void
   */
  static function testQ22(CCnStep $step) {
    // PDS-PDQ_Exact_Name
    mbTrace($step);
  }
}