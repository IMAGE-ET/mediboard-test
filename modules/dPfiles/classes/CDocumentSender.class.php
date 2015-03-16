<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Document sender abstract class
 */
abstract class CDocumentSender {
  /**
   * Function send
   *
   * @param CDocumentItem $docItem Document
   *
   * @return void
   */
  function send(CDocumentItem $docItem) {
  }

  /**
   * Function cancel
   *
   * @param CDocumentItem $docItem Document
   *
   * @return void
   */
  function cancel(CDocumentItem $docItem) {
  }

  /**
   * Function resend
   *
   * @param CDocumentItem $docItem Document
   *
   * @return void
   */
  function resend(CDocumentItem $docItem) {
  }

  /**
   * Get send problem
   *
   * @param CDocumentItem $docItem Document
   *
   * @return void
   */
  function getSendProblem(CDocumentItem $docItem) {
  }
}
