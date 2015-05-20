<?php
/**
 * $Id: CRPU.class.php 20203 2013-08-20 10:19:16Z flaviencrochard $
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20203 $
 */

/**
 * Class CRPUSender
 */
abstract class CRPUSender {
  /**
   * Load extract passages
   *
   * @param CExtractPassages &$extractPassages Passages
   *
   * @return void
   */
  function loadExtractPassages(CExtractPassages &$extractPassages) {
  }

  /**
   * Extract
   *
   * @param CExtractPassages $extractPassages Passage
   * @param CRPU[]           $rpus            RPUS
   *
   * @return void
   */
  function extract(CExtractPassages $extractPassages, $rpus) {
  }

  /**
   * Extract RPU
   *
   * @param CExtractPassages $extractPassages Passage
   * @param CRPU[]           $rpus            RPUS
   *
   * @return void
   */
  function extractRPU(CExtractPassages $extractPassages, $rpus) {
  }

  /**
   * Extract URG
   *
   * @param CExtractPassages $extractPassages Passage
   * @param CRPU[]           $rpus            RPUS
   *
   * @return void
   */
  function extractURG(CExtractPassages $extractPassages, $rpus) {
  }

  /**
   * Extract UHCD
   *
   * @param CExtractPassages $extractPassages Passage
   * @param CRPU[]           $rpus            RPUS
   *
   * @return void
   */
  function extractUHCD(CExtractPassages $extractPassages, $rpus) {
  }

  /**
   * Extract acticity
   *
   * @param CExtractPassages $extractPassages Passage
   * @param CRPU[]           $rpus            RPUS
   *
   * @return void
   */
  function extractActivite(CExtractPassages $extractPassages, $rpus) {
  }

  /**
   * Encrypt extract passages
   *
   * @param CExtractPassages $extractPassages Passages
   *
   * @return void
   */
  function encrypt(CExtractPassages $extractPassages) {
  }

  /**
   * Transmit extract passages
   *
   * @param CExtractPassages $extractPassages Passages
   *
   * @return void
   */
  function transmit(CExtractPassages $extractPassages) {
  }

  /**
   * Show encrypt key
   *
   * @return string
   */
  function showEncryptKey() {
  }
}
