<?php

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author
 */

class CDMPSender extends CDocumentSender {

  function send(CCompteRendu $docItem) {
    
    if ($xml = CDMPAntaresXML::generateXML($docItem)) {
      return true;
    }
    else {
      CAppUI::stepAjax("Document non valide", UI_MSG_ERROR);
    }
  }
  
  function cancel(CCompteRendu $docItem) {
  }
  
  function resend(CCompteRendu $docItem) {
  }
  
  function getSendProblem(CCompteRendu $docItem) {
    
  }
}