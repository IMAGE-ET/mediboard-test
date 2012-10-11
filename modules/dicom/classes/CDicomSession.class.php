<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage 
 * @version $Revision$
 * @author SARL OpenXtrem
 */

/**
 * 
 */
class CDicomSession extends CMbObject {
  
  const sta1 = "Sta1";
  
  const sta2 = "Sta2";
  
  const sta3 = "Sta3";
  
  const sta4 = "Sta4";
  
  const sta5 = "Sta5";
  
  const sta6 = "Sta6";
  
  const sta7 = "Sta7";
  
  const sta8 = "Sta8";
  
  const sta9 = "Sta9";
  
  const sta10 = "Sta10";
  
  const sta11 = "Sta11";
  
  const sta12 = "Sta12";
  
  const sta13 = "Sta13";
  
  /**
   * The id of the session
   * 
   * @var integer
   */
  public $dicom_session_id = null;
  
  /**
   * The address of the actor who receive the association request.
   * If null, the receiver is Mediboard.
   * 
   * @var string
   */
  public $receiver = null;
  
  /**
   * The address of the actor who initiate the session.
   * If null, the sender is Mediboard.
   * 
   * @var string
   */
  public $sender = null;
  
  /**
   * The begin datetime of the session
   * 
   * @var datetime
   */
  public $begin_date = null;
  
  /**
   * The end datetime of the session
   * 
   * @var datetime
   */
  public $end_date = null;
  
  /**
   * The messages send and received during the session.
   * The structure is : 'type/message|type/message|...'
   * @var string
   */
  public $messages = null;
  
  /**
   * The id of the Dicom exchange, containing only the data pdus
   * 
   * @var integer
   */
  public $dicom_exchange_id = null;
  
  /**
   * The messages
   * The key is the type, and the value is the message.
   * 
   * @var array
   */
  public $_messages = null;
  
  /**
   * The ARTIM timer
   * 
   * @var
   */
  protected $_artim_timer = null;
  
  /**
   * The current state of the DICOM UL State machine
   * 
   * @var string
   */
  protected $_state = null;
  
  /**
   * The presentation context
   * 
   * @var CDicomItemPresentationContext
   */
  protected $_presentation_context = null;
  
  /**
   * The last PDU received
   * 
   * @var CDicomPDU
   */
  protected $_last_PDU_received = null;
  
  /**
   * The Dicom exchange, containing only the data pdus
   * 
   * @var CExchangeDicom
   */
  protected $_ref_dicom_exchange = null;
  
  /**
   * The actor, the sender or the receiver
   * 
   * @var CInteropActor
   */
  protected $_ref_actor = null;
  
  /**
   * The DICOM UL state machine
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @var array
   */
  protected static $_state_machine = array(
    "Sta1" => array(
      "AAssociateRQ_Prepared" => "AE_1",
      "TCP_Open"              => "AE_5",
    ),
    "Sta2" => array(
      "AAssociateAC_Received" => "AA_1",
      "AAssociateRJ_Received" => "AA_1",
      "AAssociateRQ_Received" => "AE_6",
      "PDataTF_Received"      => "AA_1",
      "AReleaseRQ_Received"   => "AA_1",
      "AReleaseRP_Received"   => "AA_1",
      "AAbort_Received"       => "AA_2",
      "TCP_Closed"            => "AA_5",
      "ARTIMTimeOut"          => "AA_2",
      "InvalidPDU"            => "AA_1",
    ),
    "Sta3" => array(
      "AAssociateAC_Received" => "AA_8",
      "AAssociateRJ_Received" => "AA_8",
      "AAssociateRQ_Received" => "AA_8",
      "AAssociateAC_Prepared" => "AE_7",
      "AAssociateRJ_Prepared" => "AE_8",
      "PDataTF_Received"      => "AA_8",
      "AReleaseRQ_Received"   => "AA_8",
      "AReleaseRP_Received"   => "AA_8",
      "AAbort_Prepared"       => "AA_1",
      "AAbort_Received"       => "AA_3",
      "TCP_Closed"            => "AA_4",
      "InvalidPDU"            => "AA_8",
    ),
    "Sta4" => array(
      "TCP_Indication"        => "AE_2",
      "AAbort_Prepared"       => "AA_2",
      "TCP_Closed"            => "AA_4",
    ),
    "Sta5" => array(
      "AAssociateAC_Received" => "AE_3",
      "AAssociateRJ_Received" => "AE_4",
      "AAssociateRQ_Received" => "AA_8",
      "PDataTF_Received"      => "AA_8",
      "AReleaseRQ_Received"   => "AA_8",
      "AReleaseRP_Received"   => "AA_8",
      "AAbort_Prepared"       => "AA_1",
      "AAbort_Received"       => "AA_3",
      "TCP_Closed"            => "AA_4",
      "InvalidPDU"            => "AA_8",
    ),
    "Sta6" => array(
      "AAssociateAC_Received" => "AA_8",
      "AAssociateRJ_Received" => "AA_8",
      "AAssociateRQ_Received" => "AA_8",
      "PDataTF_Prepared"      => "DT_1",
      "PDataTF_Received"      => "DT_2",
      "AReleaseRQ_Prepared"   => "AR_1",
      "AReleaseRQ_Received"   => "AR_2",
      "AReleaseRP_Received"   => "AA_8",
      "AAbort_Prepared"       => "AA_1",
      "AAbort_Received"       => "AA_3",
      "TCP_Closed"            => "AA_4",
      "InvalidPDU"            => "AA_8",
    ),
    "Sta7" => array(
      "AAssociateAC_Received" => "AA_8",
      "AAssociateRJ_Received" => "AA_8",
      "AAssociateRQ_Received" => "AA_8",
      "PDataTF_Received"      => "AR_6",
      "AReleaseRQ_Received"   => "AR_8",
      "AReleaseRP_Received"   => "AR_3",
      "AAbort_Prepared"       => "AA_1",
      "AAbort_Received"       => "AA_3",
      "TCP_Closed"            => "AA_4",
      "InvalidPDU"            => "AA_8",
    ),
    "Sta8" => array(
      "AAssociateAC_Received" => "AA_8",
      "AAssociateRJ_Received" => "AA_8",
      "AAssociateRQ_Received" => "AA_8",
      "PDataTF_Prepared"      => "AR_7",
      "PDataTF_Received"      => "AA_8",
      "AReleaseRQ_Received"   => "AA_8",
      "AReleaseRP_Received"   => "AA_8",
      "AReleaseRP_Prepared"   => "AR_4",
      "AAbort_Prepared"       => "AA_1",
      "AAbort_Received"       => "AA_3",
      "TCP_Closed"            => "AA_4",
      "InvalidPDU"            => "AA_8",
    ),
    "Sta9" => array(
      "AAssociateAC_Received" => "AA_8",
      "AAssociateRJ_Received" => "AA_8",
      "AAssociateRQ_Received" => "AA_8",
      "PDataTF_Received"      => "AA_8",
      "AReleaseRQ_Received"   => "AA_8",
      "AReleaseRP_Received"   => "AA_8",
      "AReleaseRP_Prepared"   => "AR_9",
      "AAbort_Prepared"       => "AA_1",
      "AAbort_Received"       => "AA_3",
      "TCP_Closed"            => "AA_4",
      "InvalidPDU"            => "AA_8",
    ),
    "Sta10" => array(
      "AAssociateAC_Received" => "AA_8",
      "AAssociateRJ_Received" => "AA_8",
      "AAssociateRQ_Received" => "AA_8",
      "PDataTF_Received"      => "AA_8",
      "AReleaseRQ_Received"   => "AA_8",
      "AReleaseRP_Received"   => "AR_10",
      "AAbort_Prepared"       => "AA_1",
      "AAbort_Received"       => "AA_3",
      "TCP_Closed"            => "AA_4",
      "InvalidPDU"            => "AA_8",
    ),
    "Sta11" => array(
      "AAssociateAC_Received" => "AA_8",
      "AAssociateRJ_Received" => "AA_8",
      "AAssociateRQ_Received" => "AA_8",
      "PDataTF_Received"      => "AA_8",
      "AReleaseRQ_Received"   => "AA_8",
      "AReleaseRP_Received"   => "AR_3",
      "AAbort_Prepared"       => "AA_1",
      "AAbort_Received"       => "AA_3",
      "TCP_Closed"            => "AA_4",
      "InvalidPDU"            => "AA_8",
    ),
    "Sta12" => array(
      "AAssociateAC_Received" => "AA_8",
      "AAssociateRJ_Received" => "AA_8",
      "AAssociateRQ_Received" => "AA_8",
      "PDataTF_Received"      => "AA_8",
      "AReleaseRQ_Received"   => "AA_8",
      "AReleaseRP_Received"   => "AA_8",
      "AReleaseRP_Prepared"   => "AR_4",
      "AAbort_Prepared"       => "AA_1",
      "AAbort_Received"       => "AA_3",
      "TCP_Closed"            => "AA_4",
      "InvalidPDU"            => "AA_8",
    ),
    "Sta13" => array(
      "AAssociateAC_Received" => "AA_6",
      "AAssociateRJ_Received" => "AA_6",
      "AAssociateRQ_Received" => "AA_7",
      "PDataTF_Received"      => "AA_6",
      "AReleaseRQ_Received"   => "AA_6",
      "AReleaseRP_Received"   => "AA_6",
      "AAbort_Received"       => "AA_2",
      "TCP_Closed"            => "AR_5",
      "ARTIM_TimeOut"         => "AA_2",
      "InvalidPDU"            => "AA_7",
    ),
  );
  
  /**
   * The constructor
   * 
   * 
   */
  function __construct($actor) {
    $this->setActor($actor);
    $this->_state = "sta1";
    $this->begin_date = mbDateTime();
    $this->messages = "";
  }
  
  /**
   * Initialize the class specifications
   * 
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "dicom_session";
    $spec->key    = "dicom_session_id";
    
    return $spec; 
  }
  
  /**
   * Get the properties of our class as string
   * 
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["receiver"]          = "str notNull";
    $props["sender"]            = "str notNull";
    $props["begin_date"]        = "dateTime notNull";
    $props["end_date"]          = "dateTime";
    $props["messages"]          = "str notNull";
    $props["dicom_exchange_id"] = "ref class|CExchangeDicom";
    return $props;
  }
  
  function loadRefDicomExchange() {
    if ($this->dicom_exchange_id !== null && $this->_ref_dicom_exchange === null) {
      $this->_ref_dicom_exchange = new CDicomExchange();
    }
  }
  
  /**
   * Set the actor
   * 
   * @param CInteropActor $actor The actor
   * 
   * @return null
   */
  function setActor(CInteropActor $actor) {
    if (get_class($actor) == "CDicomSender" ) {
      $actor->loadRefExchangeSource();
      $this->sender = $actor->_ref_exchange_source->host . ":" . $actor->_ref_exchange_source->port;
      $this->receiver = "[SELF]";
    }
    elseif (get_class($actor) == "CDicomReceiver" ) {
      $actor->loadRefExchangeSource();
      $this->receiver = $actor->_ref_exchange_source->host . ":" . $actor->_ref_exchange_source->port;
      $this->sender = "[SELF]";
    } 
    $this->_actor = $actor;
  }
  
  /**
   * Return the actor
   * 
   * @return CInteropActor
   */
  function getActor() {
    return $this->_actor;
  }
  
  /**
   * Add a message to the field messages
   * 
   * @param string $type    The type of the message
   * 
   * @param string $message The message
   * 
   * @return null
   */
  function addMessage($type, $message) {
    if (!$this->messages) {
      $this->messages = "$type/$message";
    }
    else {
      $this->messages .= "|$type/$message";
    }
  }
  
  /**
   * Update the form fields
   * 
   * @return nulls
   */
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_messages = array();
    
    $msg_array = explode('|', $this->messages);
    
    foreach ($msg_array as $msg) {
      $msg = explode('/', $msg);
      $this->_messages[$msg[0]] = $msg[1];
    }
  }
  
  /**
   * Return the action corresponing to the current state and to the event
   * 
   * @return string
   */
  protected function getAction($event) {
    return self::$_state_machine[$this->_state][$event];
  }
  
  /**
   * Check the validity of the A-Associate-RQ PDU
   * 
   * @param CDicomPDUAAssociateRQ $pdu The A-Associate-RQ PDU
   * 
   * @return boolean
   */
  protected function isAAssociateRQValid(CDicomPDUAAssociateRQ $pdu) {
    if ($pdu->protocol_version != 0x001) {
      return 2;
    }
    
    if (strtolower($pdu->called_AE_title) != "mediboard") {
      return 7;
    }
    
    foreach ($pdu->presentation_contexts as $presentation_context) {
      $is_one_sop_class_supported = false;
      if (CDicomDictionary::isSOPClassSupported($presentation_context->abstract_syntax->name)) {
        $is_one_sop_class_supported = true;
      }
      
      $is_one_transfer_syntax_supported = false;
      foreach ($presentation_context->transfer_syntaxes as $transfer_syntax) {
        if (CDicomDictionary::isTransferSyntaxSupported($transfer_syntax->name)) {
          $is_one_transfer_syntax_supported = true;
        }
      }
      
      if (!$is_one_sop_class_supported || !$is_one_transfer_syntax_supported) {
        return 1;
      }
    }
    
    return true;
  }
  
  /**
   * Prepare the datas for creating a A-Associate-AC PDU
   * 
   * @todo handle the user info sub items
   * 
   * @param CDicomPDUAAssociateRQ $associate_rq The A-Associate-RQ PDU
   * 
   * @return array
   */
  protected function prepareAAssociateACPDU(CDicomPDUAAssociateRQ $associate_rq) {
    $datas = array(
      "protocol_version"      => 1,
      "called_AE_title"       => $associate_rq->called_AE_title,
      "calling_AE_title"      => $associate_rq->calling_AE_title,
      "application_context"   => array("name" => $associate_rq->application_context->name),
      "presentation_contexts" => array(),
      "user_info"             => array(
        "CDicomPDUItemMaximumLength" => 32768,
        "CDicomPDUItemImplementationClassUID" => "1.2.250.1.2.3.4",
        "CDicomPDUItemImplementationVersionName" => "mediboard",
      ),
    );
      
    foreach ($associate_rq->presentation_contexts as $presentation_context) {
      $reason = 0;
      if (!CDicomDictionary::isSOPClassSupported($presentation_context->abstract_syntax->name)) {
        $reason = 3;
      }
      
      $transfer_syntax = "";
      $transfer_syntaxes = array();
      foreach ($presentation_context->transfer_syntaxes as $transfer_syntax) {
        if (CDicomDictionary::isTransferSyntaxSupported($transfer_syntax->name)) {
          $transfer_syntaxes[] = $transfer_syntax->name;
        }
      }
      
      if (in_array("1.2.840.10008.1.2", $transfer_syntaxes)) {
        $transfer_syntax = "1.2.840.10008.1.2";
      }
      else {
        if (count($transfer_syntaxes) == 0) {
          $reason = 4;
        }
        $transfer_syntax = $transfer_syntaxes[0];
      }
      $datas["presentation_contexts"][] = array(
        "id" => $presentation_context->id,
        "reason" => $reason,
        "transfer_syntax" => array("name" => $transfer_syntax),
      );
    }
    return $datas;
  }

  /**
   * Prepare the datas for creating a A-Associate-RJ PDU
   * 
   * @param integer $reason The reason of the reject
   * 
   * @return array
   */
  protected function prepareAAssociateRJPDU($reason) {
    return array(
      "result" => 2,
      "source" => 1,
      "diagnostic" => $reason,
    );
  }
  
  /**
   * Handle an event
   * 
   * @param string $event The name of the event
   * 
   * @param string $datas The datas
   * 
   * @return string
   */
  function handleEvent($event, $datas = null) {
    $action = $this->getAction($event);
    $method = "do$action";
    
    if (method_exists($this, $method)) {
      return $this->$method($datas);
    }
    else {
      /** @todo Lever une exception **/
    }
  }
  
  /**
   * The action AE-1
   * 
   * Open a TCP connection with the server, only used in client mode
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAE_1($datas) {
    // Open a TCP Connection with the server
  }
  
  /**
   * The action AE-2
   * 
   * Send the prepared A-ASSOCIATE-RQ PDU to the server, only used in client mode
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAE_2($datas) {
    
  }
  
  /**
   * The action AE-3
   * 
   * Decode a A-ASSOCIATE-AC PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAE_3($datas) {
    $associate_ac = CDicomPDUFactory::decodePDU($datas);
    
    $this->_last_PDU_received = $associate_ac;
    $this->addMessage($associate_ac->type_str, $associate_ac->getPacket());
    
    $this->_state = self::sta6;
    
  }
  
  /**
   * The action AE-4
   * 
   * Decode a A-ASSOCIATE-RJ PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAE_4($datas) {
    $associate_rj = CDicomPDUFactory::decodePDU($datas);
    
    $this->_last_PDU_received = $associate_rj;
    $this->addMessage($associate_rj->type_str, $associate_rj->getPacket());
    
    $this->_state = self::sta1;
    /** Close connection **/
  }
  
  /**
   * The action AE-5
   * 
   * Start ARTIM timer, wait for A-ASSOCIATE-RQ
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAE_5($datas) {
    // start ARTIM timer
    $this->_state = self::sta2;
  }
  
  /**
   * The action AE-6
   * 
   * Stop ARTIM timer, and decode the A-ASSOCIATE-RQ PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAE_6($datas) {
    // stop ARTIM timer
    $associate_rq = CDicomPDUFactory::decodePDU($datas);
    
    $this->_last_PDU_received = $associate_rq;
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $valid = $this->isAAssociateRQValid($associate_rq);
    
    $this->_state = self::sta3;
    
    if ($valid === true) {
      
      return $this->handleEvent("AAssociateAC_Prepared", $this->prepareAAssociateACPDU($associate_rq));
    }
    else {
      return $this->handleEvent("AAssociateRJ_Prepared", $this->prepareAAssociateRJPDU($valid));
    }
  }
  
  /**
   * The action AE-7
   * 
   * Send A-ASSOCIATE-AC PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAE_7($datas) {
    $pdu = CDicomPDUFactory::encodePDU("02", $datas);
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $this->_state = self::sta6;
    return $pdu->getPacket();
  }
  
  /**
   * The action AE-8
   * 
   * Send A-ASSOCIATE-RJ PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAE_8($datas) {
    $pdu = CDicomPDUFactory::encodePDU("03", $datas);
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $this->_state = self::sta13;
    return $pdu->getPacket();
  }
  
  /**
   * The action DT-1
   * 
   * Send a P-DATA-TF PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doDT_1($datas) {
    
  }
  
  /**
   * The action DT-2
   * 
   * Decode the P-DATA-FT PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doDT_2($datas) {
    
  }
  
  /**
   * The action AR-1
   * 
   * Send a A-RELEASE-RQ PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_1($datas = null) {
    $pdu = CDicomPDUFactory::encodePDU("05", $datas);
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $this->_state = self::sta7;
    return $pdu->getPacket();
  }
  
  /**
   * The action AR-2
   * 
   * Decode the A-RELEASE-RQ PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_2($datas) {
    $release_rq = CDicomPDUFactory::decodePDU($datas);
    
    $this->_last_PDU_received = $release_rq;
    $this->addMessage($release_rq->type_str, $release_rq->getPacket());
    
    $this->_state = self::sta8;
    
    return $this->handleEvent("AReleaseRP_Prepared");
  }
  
  /**
   * The action AR-3
   * 
   * Decode the A-RELEASE-RP PDU
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_3($datas) {
    $release_rp = CDicomPDUFactory::decodePDU($datas);
    
    $this->_last_PDU_received = $release_rp;
    $this->addMessage($release_rp->type_str, $release_rp->getPacket());
    
    $this->_state = self::sta1;
    
    /** @todo close the connection **/
    return "";
  }
  
  /**
   * The action AR-4
   * 
   * Send a A-RELEASE-RP PDU and start ARTIM timer
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_4($datas = null) {
    $pdu = CDicomPDUFactory::encodePDU("06");
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $this->_state = self::sta13;
    /** @todo start ARTIM timer **/
    return $pdu->getPacket();
  }
  
  /**
   * The action AR-5
   * 
   * Stop ARTIM timer
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_5($datas) {
    /** @todo stop ARTIM timer **/
    $this->_state = self::sta1;
  }
  
  /**
   * The action AR-6
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_6($datas) {
    
  }
  
  /**
   * The action AR-7
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_7($datas) {
    
  }
  
  /**
   * The action AR-8
   * 
   * Handle the case of A-Release-RQ collision
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_8($datas) {
    $release_rq = CDicomPDUFactory::decodePDU($datas);
    
    $this->_last_PDU_received = $release_rq;
    $this->addMessage($release_rq->type_str, $release_rq->getPacket());
    
    if ($this->sender) {
      $this->_state = self::sta9;
      return $this->handleEvent("AReleaseRP_Prepared");      
    }
    else {
      $this->_state = self::sta9;
      return "";
    }
  }
  
  /**
   * The action AR-9
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_9($datas = null) {
    $pdu = CDicomPDUFactory::encodePDU("06");
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $this->_state = self::sta11;
    
    return $pdu->getPacket();
  }
  
  /**
   * The action AR-10
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAR_10($datas) {
    $release_rp = CDicomPDUFactory::decodePDU($datas);
    
    $this->_last_PDU_received = $release_rp;
    $this->addMessage($release_rp->type_str, $release_rp->getPacket());
    
    $this->_state = self::sta12;
    return $this->handleEvent("AReleaseRP_Prepared");      
  }
  
  /**
   * The action AA-1
   * 
   * Send a A-ABORT PDU and start ARTIM timer
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAA_1($datas) {
    $diagnostic = 0;
    if ($datas && is_integer($datas)) {
      $diagnostic = $datas;
    }
    $pdu = CDicomPDUFactory::encodePDU("07", array("source" => 0, "diagnostic" => $diagnostic));
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $this->_state = self::sta13;
    
    return $pdu->getPacket();
  }
  
  /**
   * The action AA-2
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAA_2($datas) {
    /**
     Stop ARTIM timer
     Close connection
     **/
    $this->_state = self::sta1;
  }
  
  /**
   * The action AA-3
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAA_3($datas) {
    $pdu = CDicomPDUFactory::decodePDU($datas);
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    /** close connection **/
    $this->_state = self::sta1;
  }
  
  /**
   * The action AA-4
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAA_4($datas) {
    $pdu = CDicomPDUFactory::decodePDU($datas);
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $this->_state = self::sta1;
  }
  
  /**
   * The action AA-5
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAA_5($datas) {
    /** Stop ARTIM timer **/  
    $this->_state = self::sta1;
  }
  
  /**
   * The action AA-6
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAA_6($datas) {
    $this->_state = self::sta13;
  }
  
  /**
   * The action AA-7
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAA_7($datas) {
    $pdu = CDicomPDUFactory::encodePDU("07", array("source" => 2, "diagnostic" => 0));
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $this->_state = self::sta13;
    
    return $pdu->getPacket();
  }
  
  /**
   * The action AA-8
   * 
   * @see DICOM Standard PS 3.8 Section 9.2
   * 
   * @param mixed $datas The datas
   * 
   * @return string
   */
  protected function doAA_8($datas) {
    $diagnostic = 0;
    if ($datas && is_integer($datas)) {
      $diagnostic = $datas;
    }
    $pdu = CDicomPDUFactory::encodePDU("07", array("source" => 2, "diagnostic" => $diagnostic));
    
    $this->addMessage($pdu->type_str, $pdu->getPacket());
    
    $this->_state = self::sta13;
    
    return $pdu->getPacket();
  }
}
?>