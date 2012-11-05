<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */ 
 
/**
 * A Dicom exchange
 */
class CExchangeDicom extends CExchangeBinary {
  
  static $messages = array(
    "Echo" => "CEcho",
    "Find" => "CFind",
  );
  
  /**
   * Table Key
   *
   * @var integer
   */
  var $exchange_dicom_id = null;
  
  /**
   * The request
   * If there is several messages, they are separated by "|"
   * 
   * @var string
   */
  var $requests = null;
  
  /**
   * The response
   * If there is several messages, they are separated by "|"
   * 
   * @var string
   */
  var $responses = null;
  
  /**
   * The presentation contexts, in string
   * 
   * @var string
   */
  public $presentation_contexts = null;
  
  /**
   * The request
   * 
   * @var array
   */
  var $_requests = null;
  
  /**
   * The response
   * 
   * @var array
   */
  var $_responses = null;
  
  /**
   * The presentation contexts
   * 
   * @var array-of-CDicomPresentationContext
   */
  var $_presentation_contexts = null;
  
  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table	= "dicom_exchange";
    $spec->key		= "dicom_exchange_id";
    return $spec;	
  }
  
  /**
   * Get the properties of our class as string
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["requests"]              = "text notNull";
    $props["responses"]             = "text notNull";
    $props["presentation_contexts"] = "str show|0";
    //$props["receiver_id"]         = "ref class|CDicomReceiver";
    $props["sender_class"]          = "enum list|CDicomSender show|0";
    $props["object_class"]          = "str";
    return $props;
  }
  
  /**
   * Decode the messages
   * 
   * @return null
   */
  function loadContent() {
    $request_msgs = explode("|", $this->requests);
    $this->_requests = array();
    
    foreach ($request_msgs as $msg) {
      $pdu = CDicomPDUFactory::decodePDU($msg, $this->_presentation_contexts);
      $this->_requests[] = $pdu;
    }
    
    $response_msgs = explode("|", $this->responses);
    $this->_responses = array();
    
    foreach ($response_msgs as $msg) {
      $pdu = CDicomPDUFactory::decodePDU($msg, $this->_presentation_contexts);
      $this->_responses[] = $pdu;
    }
  }
  
  /**
   * Update the form fields
   * 
   * @return nulls
   */
  function updateFormFields() {
    if ($this->presentation_contexts) {
      $pres_contexts_array = explode('|', $this->presentation_contexts);
      $this->_presentation_contexts = array();
      
      foreach ($pres_contexts_array as $_pres_context) {
        $_pres_context = explode('/', $_pres_context);
        
        $this->_presentation_contexts[] = new CDicomPresentationContext($_pres_context[0], $_pres_context[1], $_pres_context[2]);
      }
    }
    
    parent::updateFormFields();
  }
  
  /**
   * Update the fields tored in database
   * 
   * @return null
   */
  function updatePlainFields() {
    parent::updatePlainFields();
    
    if ($this->_presentation_contexts && !$this->presentation_contexts) {
      foreach ($this->_presentation_contexts as $_pres_context) {
        if (!$this->presentation_contexts) {
          $this->presentation_contexts = "$_pres_context->id/
            $_pres_context->abstract_syntax/$_pres_context->transfer_syntax";
        }
        else {
          $this->presentation_contexts .= "|$_pres_context->id/
            $_pres_context->abstract_syntax/$_pres_context->transfer_syntax";
        }
      }
    }
    
    if ($this->_requests) {
      $this->requests = null;
      foreach ($this->_requests as $_request) {
        if (!$this->requests) {
          $this->requests = $_request->getPacket();
        }
        else {
          $this->requests .= "|" . $_request->getPacket();
        }  
      }
    }
    
    if ($this->_responses) {
      $this->responses = null;
      foreach ($this->_responses as $_response) {
        if (!$this->responses) {
          $this->responses = $_response->getPacket();
        }
        else {
          $this->responses .= "|" . $_response->getPacket();
        }  
      }
    }
  }
  
  /**
   * Handle the message
   * 
   * @return array
   */
  function handle() {
    return COperatorDicom::event($this);
  }
  
  /**
   * Return the family
   * 
   * @return array
   */
  function getFamily() {
    return self::$messages;
  }
  
  /**
   * Check if the message is well formed
   * 
   * @param string        $msg   The message
   * 
   * @param CInteropActor $actor The actor who sent the message
   * 
   * @return true
   */
  function isWellFormed($msg, CInteropActor $actor = null) {
    $stream = fopen("php://temp", 'w+');
    fwrite($stream, $msg);
    
    $stream_reader = new CDicomStreamReader($stream);
    $stream_reader->rewind();
    $type = $stream_reader->readHexByte();

    if ($type != "04") {
      $stream_reader->close();
      return false;
    }
    
    $stream_reader->skip(1);
    $length = $stream_reader->readUInt32();
    $stream_reader->close();
    
    if (strlen($msg) != $length + 6) {
      return false;
    }
    
    return true;
  }
  
  /**
   * Check if we can understand the message
   * 
   * @param string        $msg           The message
   * 
   * @param CInteropActor $actor         The actor
   * 
   * @param array         $pres_contexts The presentation contexts
   * 
   * @return boolean
   */
  function understand($msg , CInteropActor $actor = null, $pres_contexts = null) {
    $this->_presentation_contexts = $pres_contexts;  
    if (!$this->isWellFormed($msg)) {
      return false;
    }
    
    $pdu = CDicomPDUFactory::decodePDU($msg, $this->_presentation_contexts);
    
    if (!$this->_requests) {
      $this->_requests = array();
    }
    $pdv = $pdu->getPDV();
    $this->_requests[] = $pdu;
    
    $msg = $pdv->getMessage();
    foreach ($this->getFamily() as $_family) {
      $family_class = new $_family;
      $events = $family_class->getEvenements();
      if (array_key_exists($msg->type, $events)) {
        $this->_family_message_class = $_family;
        $this->_family_message = get_class($msg);
        $this->message_valide = 1;
        return true;
      }
    }
    return false;
  }
}
?>