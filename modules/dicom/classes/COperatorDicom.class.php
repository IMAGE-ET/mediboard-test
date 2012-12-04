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
    if ($data_format->_exchange_id) {
      $old_requests = $data_format->_requests;
      $pres_contexts = $data_format->_presentation_contexts;
      $dicom_exchange->load($data_format->_exchange_id);
      $dicom_exchange->decodeContent();
      $dicom_exchange->_presentation_contexts = $pres_contexts;
      $dicom_exchange->_requests = array_merge($dicom_exchange->_requests, $old_requests);
    }
    else {
      $dicom_exchange = $data_format;
      $dicom_exchange->date_production = mbDatetime();
      $dicom_exchange->date_echange = mbDatetime();
    }
    $last_pdvs = $dicom_exchange->_requests[count($dicom_exchange->_requests) - 1]->getPDVs();

    $response = array();
    $time_deb_pdv = microtime(true);
    foreach ($last_pdvs as $last_pdv) {
      if ($last_pdv->getMessageControlHeader() == 0 || $last_pdv->getMessageControlHeader() == 2) {
        $find_rq_pdv = null;

        if (count($last_pdvs) > 1) {
          $find_rq_pdv = $last_pdvs[0];
        }
        else {
          for ($i = count($dicom_exchange->_requests) - 2; $i >= 0; $i--) {
            $_pdv = $dicom_exchange->_requests[$i]->getPDV(0);
            if ($_pdv->getMessage()->type == "C-Find-RQ") {
              $find_rq_pdv = $_pdv;
              break;
            }
          }
        }

        if (!$find_rq_pdv) {
          return null;
        }
        $response = self::handleCDatas($find_rq_pdv, $last_pdv, $dicom_exchange);
      }
      else {
        // The PDV contain a header
        $msg = $last_pdv->getMessage();
        switch ($msg->getCommandField()->getValue()) {
          case 0x0030 :
            $response = self::handleCEchoRQ($last_pdv, $dicom_exchange);
            break;
          case 0x8030 :
            $response = self::handleCEchoRSP($last_pdv, $dicom_exchange);
            break;
          case 0x0020 :
            $response = self::handleCFindRQ($last_pdv, $dicom_exchange);
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
    }
    $time_aft_pdv = microtime(true);
    $dicom_exchange->store();
    $response["exchange_id"] = $dicom_exchange->_id;

    return $response;
  }

  /**
   * Handle a C-Echo-RQ message, and return the answer
   * 
   * @param CDicomPDV      $pdv            The PDV who contains the C-Echo-RQ message
   * @param CExchangeDicom $dicom_exchange The Exchange Dicom
   * 
   * @return array
   */
  protected function handleCEchoRQ($pdv, $dicom_exchange) {
    $msg = $pdv->getMessage();
    $datas = array(
      "PDVs" => array(
        array(
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
      ),
    );
    $pdu = CDicomPDUFactory::encodePDU("04", $datas, $dicom_exchange->_presentation_contexts);
    
    if (!$dicom_exchange->_responses) {
      $dicom_exchange->_responses = array();
    }
    $dicom_exchange->_responses[] = $pdu;
    $dicom_exchange->acquittement_valide = 1;
    $dicom_exchange->type = "Echo";

    return array("event" => "PDataTF_Prepared", "datas" => $pdu);
  }
  
  /**
   * Handle a C-Echo-RQ message, and return the answer
   * 
   * @return array
   */
  protected function handleCEchoRSP() {
    return array("event" => "AReleaseRQ_Prepared", "datas" => null);
  }
  
  /**
   * Handle a C-Find-RQ message, and return the answer
   * 
   * @param CDicomPDV      $pdv            The PDV who contains the C-Echo-RQ message
   * @param CExchangeDicom $dicom_exchange The Exchange Dicom
   * 
   * @return array
   */
  protected function handleCFindRQ($pdv, $dicom_exchange) {
    $msg = $pdv->getMessage();
    
    // if the message does not contains datas :
    if ($msg->getCommandDataSet()->getValue() == 0x0101 && $msg->getAffectedSopClass()->getValue() == '1.2.840.10008.5.1.4.31') {
      return array("event" => "AAbort_Prepared", "datas" => 5);
    }
    return array();
  }
  
  /**
   * Handle a C-Find-RSP message, and return the answer
   * 
   * @return array
   */
  protected function handleCFindRSP() {
    return array("event" => "AAbort_Prepared", "datas" => 5);
  }
  
  /**
   * Handle a C-Cancel-Find-RQ message, and return the answer
   * 
   * @return array
   */
  protected function handleCCancelFindRQ() {
    return array("event" => "AAbort_Prepared", "datas" => 5);
  }
  
  /**
   * Handle a C-Data message, and return the answer
   *
   * @param CDicomPDV      $find_rq_pdv    The PDV who contains the C-Find-RQ message
   * @param CDicomPDV      $find_data_pdv  The PDV who contains the C-Find-Data message
   * @param CExchangeDicom $dicom_exchange The Exchange Dicom
   *
   * @todo Le code est spécifique à la Source, il faudrait le rendre générique
   *
   * @return array
   */
  protected function handleCDatas($find_rq_pdv, $find_data_pdv, $dicom_exchange) {
    $msg_rq = $find_rq_pdv->getMessage();
    $msg_data = $find_data_pdv->getMessage();

    if (is_null($dicom_exchange->_responses)) {
      /** The message is a request : **/
      $requested_datas = $msg_data->getRequestedDatas();
      $sender = $dicom_exchange->loadRefSender();

      if (!$sender->_id) {
        return array("event" => "AAbort_Prepared", "datas" => 5);
      }

      $linked_objects = CObjectToInteropSender::loadAllObjectsFor($sender->_id);

      $find_rsp_pending_datas = array(
        "PDVs" => array(
          array(
            "pres_context_id"        => $find_data_pdv->getPresContextId(),
            "message_control_header" => 0x03,
            "message"                => array(
              "type"  => 0x8020,
              "datas" => array(
                "affected_sop_class" => $msg_rq->getAffectedSopClass()->getValue(),
                "command_field"      => 0x8020,
                "message_id_request" => $msg_rq->getMessageId()->getValue(),
                "command_data_set"   => 0xfefe,
                "status"             => 0xff00,
              ),
            ),
          ),
        ),
      );

      $find_rsp_pending = CDicomPDUFactory::encodePDU(0x04, $find_rsp_pending_datas, $dicom_exchange->_presentation_contexts);

      $responses = array();

      foreach ($linked_objects as $linked_object) {
        if ($linked_object->object_class != "CSalle") {
          continue;
        }

        $operation = new COperation;
        $where = array(
          'salle_id' => " = $linked_object->object_id",
          'date'     => " = " . mbDate()
        );
        $unplanned_operations = $operation->loadList($where);

        $ljoin = array(
          "plagesop" => "`plagesop`.`plageop_id` = `operations`.`plageop_id`"
        );

        $where = array(
          "plagesop.salle_id"   => " = $linked_object->object_id",
          "plagesop.date"       => " = " . mbDate(),
          "operations.salle_id" => " = $linked_object->object_id"
        );
        $operations = $operation->loadList($where, null, null, null, $ljoin);

        $operations = array_merge($operations, $unplanned_operations);
        foreach ($operations as $_operation) {
          $responses[] = $find_rsp_pending;

          $_patient = $_operation->loadRefPatient();
          $_operation->updateFormFields();
          $_operation->loadRefPlageOp();
          $_sejour = $_operation->loadRefSejour();
          $_chir = $_operation->loadRefChir();
          $_patient->loadIPP();
          $_sejour->loadNDA();

          $libelle = "";
          if ($_operation->libelle) {
            $libelle = utf8_encode(substr($_operation->libelle, 0, 64));
          }
          else {
            $libelle = utf8_encode("Pas de libellé");
          }

          $date = "";
          if ($_operation->date) {
            $date = str_replace("-", "", $_operation->date);
          }
          else {
            $date = str_replace("-", "", $_operation->_ref_plageop->date);
          }

          $find_rsp_datas = array(
            "PDVs" => array(
              array(
                "pres_context_id"        => $find_data_pdv->getPresContextId(),
                "message_control_header" => 0x02,
                "message"                => array(
                  "type"  => "data",
                  "datas" => array(
                    0x0010 => array(
                      0x0020 => $_patient->_IPP,
                      0x0010 => "$_patient->nom^$_patient->prenom",
                      0x0030 => str_replace("-", "", $_patient->naissance),
                      0x0040 => $_patient->sexe
                    ),
                    0x0008 => array(
                      0x0020 => $date,
                      0x0050 => $_sejour->_NDA,
                      0x0090 => "$_chir->_user_last_name^$_chir->_user_first_name",
                      0x1030 => $libelle
                    )
                  ),
                )
              ),
            )
          );

          $responses[] = CDicomPDUFactory::encodePDU(0x04, $find_rsp_datas, $dicom_exchange->_presentation_contexts);
        }
      }
      $find_rsp_success_datas = array(
        "PDVs" => array(
          array(
            "pres_context_id"        => $find_data_pdv->getPresContextId(),
            "message_control_header" => 0x03,
            "message"                => array(
              "type"  => 0x8020,
              "datas" => array(
                "affected_sop_class" => $msg_rq->getAffectedSopClass()->getValue(),
                "command_field"      => 0x8020,
                "message_id_request" => $msg_rq->getMessageId()->getValue(),
                "command_data_set"   => 0x0101,
                "status"             => 0x0000,
              ),
            ),
          ),
        ),
      );
      $responses[] = CDicomPDUFactory::encodePDU(0x04, $find_rsp_success_datas, $dicom_exchange->_presentation_contexts);

      if (!$dicom_exchange->_responses) {
        $dicom_exchange->_responses = $responses;
      }
      else {
        array_merge($dicom_exchange->_responses, $responses);
      }

      return array("event" => "PDataTF_Prepared", "datas" => $responses);
    }
    else {
      /** The message is a response : **/
      return array("event" => "AAbort_Prepared", "datas" => 5);
    }
  }
}