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
   * @see parent::send()
   */
  function send(CDocumentItem $docItem) {

  }

  /**
   * @see parent::cancel()
   */
  function cancel(CDocumentItem $docItem) {

  }

  /**
   * @see parent::resend()
   */
  function resend(CDocumentItem $docItem) {

  }

  /**
   * @see parent::getSendProblem()
   */
  function getSendProblem(CDocumentItem $docItem) {

  }
}
