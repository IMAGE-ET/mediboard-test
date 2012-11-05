<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage 
 * @version $Revision$
 * @author SARL OpenXtrem
 */

/**
 * The COperatorDicom class
 */
class COperatorDicom extends CEAIOperator {
  
  /**
   * Handle a message, and return the response
   * 
   * @param CExchangeDicom $data_format The dicom exchange
   * 
   * @return array
   */
  function event(CExchangeDicom $data_format) {
    $dicom_exchange = new CExchangeDicom();
    if ($data_format->_id) {
      $old_requests = $data_format->_requests;
      $pres_contexts = $data_format->_presentation_contexts;
      $dicom_exchange->load($data_format->_id);
      $dicom_exchange->loadContent();
      $dicom_exchange->_presentation_contexts = $pres_contexts;
      $dicom_exchange->_requests = array_merge($dicom_exchange->_requests, $old_request);
    }
    else {
      $dicom_exchange = $data_format;
      $dicom_exchange->date_production = mbDateTime();
      $dicom_exchange->date_echange = mbDateTime();
    }
    $last_pdv = $dicom_exchange->_requests[count($dicom_exchange->_requests) - 1]->getPDV();
    
    $response = null;
    if ($last_pdv->getMessageControlHeader() == 0 || $last_pdv->getMessageControlHeader() == 2) {
      $response = self::handleCDatas();
    }
    else {
      // The PDV contain a header
      $msg = $last_pdv->getMessage();
      switch ($msg->getCommandField()->getValue()) {
        case 0x0030 :
          $response = self::handleCEchoRQ($last_pdv, $dicom_exchange);
          break;
        case 0x8030 :
          $response = self::handleCEchoRSP();
          break;
        case 0x0020 :
          $response = self::handleCFindRQ();
          break;
        case 0x8020 :
          $response = self::handleCFindRSP();
          break;
        case 0x0FFF :
          $response = self::handleCCancelFindRQ();
          break;
        default :
          break;
      }
    }

    $response["exchange_id"] = $dicom_exchange->_id;

    return $response;
  }

  /**
   * Handle a C-Echo-RQ message, and return the answer
   * 
   * @param CDicomPDV      $pdv            The PDV who contains the C-Echo-RQ message
   * 
   * @param CExchangeDicom $dicom_exchange The Exchange Dicom
   * 
   * @return string
   */
  protected function handleCEchoRQ($pdv, $dicom_exchange) {
    $msg = $pdv->getMessage();
    $datas = array(
      "PDV" => array(
        "pres_context_id"        => $pdv->getPresContextId(),
        "message_control_header" => 0x03,
        "message"                => array(
          "type"  => 0x8030,
          "datas" => array(
            "affected_sop_class" => $msg->getAffectedSopClass()->getValue(),
            "command_field"      => 0x8030,
            "message_id_request" => $msg->getMessageId()->getValue(),
            "command_data_set"   => 0x0101,
            "status"             => 0x0000,
          ),
        ),
      ),
    );
    $pdu = CDicomPDUFactory::encodePDU("04", $datas, $dicom_exchange->_presentation_contexts);
    
    if (!$dicom_exchange->_responses) {
      $dicom_exchange->_responses = array();
    }
    $dicom_exchange->_responses[] = $pdu;
    $dicom_exchange->acquittement_valide = 1;
    $dicom_exchange->type = "Find";
    
    return array("event" => "PDataTF_Prepared", "datas" => $pdu);
  }
  
  /**
   * Handle a C-Echo-RQ message, and return the answer
   * 
   * @return string
   */
  protected function handleCEchoRSP() {
    return array("event" => "AReleaseRQ_Prepared", "datas" => null);
  }
  
  /**
   * Handle a C-Find-RQ message, and return the answer
   * 
   * @return string
   */
  protected function handleCFindRQ() {
    return array("event" => "AAbort_Prepared", "datas" => 5);
  }
  
  /**
   * Handle a C-Find-RSP message, and return the answer
   * 
   * @return string
   */
  protected function handleCFindRSP() {
    return array("event" => "AAbort_Prepared", "datas" => 5);
  }
  
  /**
   * Handle a C-Cancel-Find-RQ message, and return the answer
   * 
   * @return string
   */
  protected function handleCCancelFindRQ() {
    return array("event" => "AAbort_Prepared", "datas" => 5);
  }
  
  /**
   * Handle a C-Data message, and return the answer
   * 
   * @return string
   */
  protected function handleCDatas() {
    return array("event" => "AAbort_Prepared", "datas" => 5);
  }
}
?>