<?php

/**
 * Receive MLLP
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */


$client_addr = CValue::post("client_addr");
$message     = stripslashes(CValue::post("message"));

$source_mllp 		     = new CSourceMLLP;
$source_mllp->host 	 = $client_addr;
$source_mllp->active = 1;
$source_mllp->loadMatchingObject();

if (!$source_mllp->_id) {
  /*
  $message          = new CHL7v2Message();
  $message->version = "2.5";
  $message->name    = "ACK";
  
  // Message Header 
  $MSH = CHL7v2Segment::create("MSH", $message);
  $data = array();
    
  // MSH-1: Field Separator (ST)
  $data[] = $message->fieldSeparator;  
         
  // MSH-2: Encoding Characters (ST)
  $data[] = substr($message->encoding_characters(), 1);       
   
  // MSH-3: Sending Application (HD) (optional)
  $data[] = CAppUI::conf("hl7 sending_application"); 
  
  // MSH-4: Sending Facility (HD) (optional)
  $data[] = CAppUI::conf("hl7 sending_facility");
  
  // MSH-5: Receiving Application (HD) (optional)
  $data[] = null;
  
  // MSH-6: Receiving Facility (HD) (optional)
  $data[] = null;
  
  // MSH-7: Date/Time Of Message (TS)
  $data[] = mbDateTime(); 
  
  // MSH-8: Security (ST) (optional)
  $data[] = null; 
  
  // MSH-9: Message Type (MSG)
  $data[] = array(array(
    "ACK", "A12", "ACK"
  )); 
  
  // MSH-10: Message Control ID (ST) 
  $data[] = null;
  
  // MSH-11: Processing ID (PT) 
  $data[] = (CAppUI::conf("instance_role") == "prod") ? "P" : "D";
  
  // MSH-12: Version ID (VID)     
  $data[] = CHL7v2::prepareHL7Version("2.5"); 
  
  // MSH-13: Sequence Number (NM) (optional)
  $data[] = null; 
  
  // MSH-14: Continuation Pointer (ST) (optional)
  $data[] = null; 
  
  // MSH-15: Accept Acknowledgment Type (ID) (optional)
  $data[] = null;
 
  // MSH-16: Application Acknowledgment Type (ID) (optional)
  $data[] = null;
  
  // MSH-17: Country Code (ID) (optional)
  $data[] = CHL7v2TableEntry::mapTo("399", "250"); 
  
  // MSH-18: Character Set (ID) (optional repeating)
  $data[] = CHL7v2TableEntry::mapTo("211", CApp::$encoding); 
  
  // MSH-19: Principal Language Of Message (CE) (optional)
  $data[] = array(
    "FR"
  );
  
  $MSH->fill($data);
  
  $message->appendChild($MSH);
 
  // Error
  $error = new CHL7v2Error();
  $error->message = "booh";
  
  $ERR = CHL7v2Segment::create("ERR", $message);
  $ERR->error = $error;
  $ERR->build($error);
  
  $message->appendChild($ERR);*/
  
  $now  = mbTransformTime(null, null, "%Y%m%d%H%M%S");
  $ACK  = "MSH|^~\&|".CAppUI::conf("hl7 sending_application")."|".CAppUI::conf("hl7 sending_facility").
          "|||$now||ACK|$now|P|2.5||||||".CHL7v2TableEntry::mapTo("211", CApp::$encoding);
  $ACK .= "\r"."ERR||0^0|207|E|E200^Acteur inconnu|||||||";
    
  ob_clean();
  echo $ACK;
  CApp::rip();
}

$sender_mllp = CMbObject::loadFromGuid($source_mllp->name);

// Dispatch EAI 
$ack = CEAIDispatcher::dispatch($message, $sender_mllp);

ob_clean();

echo $ack;

CApp::rip();
