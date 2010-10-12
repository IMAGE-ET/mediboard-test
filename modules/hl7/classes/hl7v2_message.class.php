<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2Message {  
  const DEFAULT_SEGMENT_TERMINATOR      = "\n";
  const DEFAULT_FIELD_SEPARATOR         = "|";
  const DEFAULT_COMPONENT_SEPARATOR     = "^";
  const DEFAULT_REPETITION_SEPARATOR    = "~";
  const DEFAULT_ESCAPE_CHARACTER        = "\\";
  const DEFAULT_SUBCOMPONENT_TERMINATOR = "&";

  static $enteredHeaders = array( "MSH", "FHS", "BHS");
   
  var $segmentTerminator     = self::DEFAULT_SEGMENT_TERMINATOR;
  var $fieldSeparator        = self::DEFAULT_FIELD_SEPARATOR;
  var $componentSeparator    = self::DEFAULT_COMPONENT_SEPARATOR;
  var $repetitionSeparator   = self::DEFAULT_REPETITION_SEPARATOR;
  var $escapeCharacter       = self::DEFAULT_ESCAPE_CHARACTER;
  var $subcomponentSeparator = self::DEFAULT_SUBCOMPONENT_TERMINATOR;

  var $version     = "5";
  var $name        = null;
  var $description = null;
  var $segments    = array();
  var $data        = null;
  
  function parseMessage($message) {
    // first tokenize the segments
    if (($message == null) || (strlen($message) < 4)) {
      throw new CHL7v2Exception($message, CHL7v2Exception::EMPTY_MESSAGE);
    }
    
    $this->data = $message;
    
    $this->fieldSeparator = $message[3];
    // valid separator
    if (!preg_match("/[^a-z0-9]/i", $this->fieldSeparator) ) {
      throw new CHL7v2Exception($message, CHL7v2Exception::INVALID_SEPARATOR);
    }

    $nextDelimiter = strpos($message, $this->fieldSeparator, 4);
    if ($nextDelimiter > 4) {
      // usualy ^
      $this->componentSeparator = $message[4];
    }
    if ($nextDelimiter > 5) {
      // usualy ~
      $this->repetitionSeparator = $message[5];
    }
    if ($nextDelimiter > 6) {
      // usualy \
      $this->escapeCharacter = $message[6];
    }
    if ($nextDelimiter > 7) {
      // usualy &
      $this->subcomponentSeparator = $message[7];
    }

    // replace the special case of ^~& with ^~\&
    if ("^~&|" == substr($message, 4, 4)) {
      $this->escapeCharacter       = "\\";
      $this->subcomponentSeparator = "&";
      $this->repetitionSeparator   = "~";
      $this->componentSeparator    = "^";
    }
    
    $this->handleSegments();
  }
  
  function handleSegments() {
    $lines = explode($this->segmentTerminator, $this->data);
    foreach ($lines as $_line) {
      try {
        $segment = new CHL7v2Segment($this);
        $segment->parseSegment($_line);
      } catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
    }
    
    $this->validate();
    mbTrace($this);
  }
  
  function validate() {
    // get version
    $this->segments[0]->getVersion();
    
    // validate all segments
    foreach ($this->segments as $_segment) {
      try {
        $_segment->validateSegment();
      } catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
    }
    
    // validate message
  }
}

?>