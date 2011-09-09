<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$msg = new CHL7v2Message;
$msg->version = "2.5";
$msg->name = "ADT_A08";

$segment = CHL7v2Segment::create("MSH", $msg);

$data = array(
  null,         // MSH-1: Field Separator (ST)
  null,        // MSH-2: Encoding Characters (ST)
  "Mediboard", // MSH-3: Sending Application (HD) (optional)
  "Mediboard_finess", // MSH-4: Sending Facility (HD) (optional)
  "no_receiver", // MSH-5: Receiving Application (HD) (optional)
  null, // MSH-6: Receiving Facility (HD) (optional)
  mbDateTime(), // MSH-7: Date/Time Of Message (TS)
  null, // MSH-8: Security (ST) (optional)
  null, // MSH-9: Message Type (MSG)
  null, // MSH-10: Message Control ID (ST) 
  null, // MSH-11: Processing ID (PT) 
  null, // MSH-12: Version ID (VID) 
  15, // MSH-13: Sequence Number (NM) (optional)
  null, // MSH-14: Continuation Pointer (ST) (optional)
  null, // MSH-15: Accept Acknowledgment Type (ID) (optional)
  null, // MSH-16: Application Acknowledgment Type (ID) (optional)
  null, // MSH-17: Country Code (ID) (optional)
  "8859/1", // MSH-18: Character Set (ID) (optional repeating)
  null, // MSH-19: Principal Language Of Message (CE) (optional)
  null, // MSH-20: Alternate Character Set Handling Scheme (ID) (optional)
  null // MSH-21: Message Profile Identifier (EI) (optional repeating) 
);
    
$segment->fill($data);
$msg->appendChild($segment);

$segment = CHL7v2Segment::create("EVN", $msg);

$data = array(
  // EVN-1: Event Type Code (ID) (optional)
  "toto",
  // EVN-2: Recorded Date/Time (TS)
  mbDateTime(),
  // EVN-3: Date/Time Planned Event (TS)(optional)
  "2011-10-01",
  // EVN-4: Event Reason Code (IS) (optional)
  // Table 062
  // 01 - Patient request
  // 02 - Physician/health practitioner order 
  // 03 - Census management
  // O  - Other 
  // U  - Unknown
  null,
  // EVN-5: Operator ID (XCN) (optional repeating)
  CUser::get(),
  // EVN-6: Event Occurred (TS) (optional)
  "2011-02-02 10:10:10",
  // EVN-7: Event Facility (HD) (optional)
  null,
);
    
$segment->fill($data);
$msg->appendChild($segment);

$msg->validate();

echo "Généré";
echo $msg->highlight_er7($msg->flatten());

echo "Parsé";
$msg2 = new CHL7v2Message;
$msg2->parse($msg);

echo $msg2->highlight_er7();