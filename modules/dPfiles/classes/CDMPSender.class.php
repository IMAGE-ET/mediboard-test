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
 * DMP document sender
 */
class CDMPSender extends CDocumentSender {
  /**
   * @see parent::send()
   */
  function send(CCompteRendu $docItem) {
    if ($xml = CDMPAntaresXML::generateXML($docItem)) {
      return true;
    }

    CAppUI::stepAjax("Document non valide", UI_MSG_ERROR);
    return false;
  }

  /**
   * @see parent::cancel()
   */
  function cancel(CCompteRendu $docItem) {
  }

  /**
   * @see parent::resend()
   */
  function resend(CCompteRendu $docItem) {
  }

  /**
   * @see parent::getSendProblem()
   */
  function getSendProblem(CCompteRendu $docItem) {
  }
}
