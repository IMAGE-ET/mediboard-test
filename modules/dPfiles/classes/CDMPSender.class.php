<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Files
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * DMP document sender
 */
class CDMPSender extends CDocumentSender {
  function send(CCompteRendu $docItem) {
    if ($xml = CDMPAntaresXML::generateXML($docItem)) {
      return true;
    }

    CAppUI::stepAjax("Document non valide", UI_MSG_ERROR);
    return false;
  }
  
  function cancel(CCompteRendu $docItem) {
  }
  
  function resend(CCompteRendu $docItem) {
  }
  
  function getSendProblem(CCompteRendu $docItem) {
  }
}
