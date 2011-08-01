<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2Message extends CHL7v2 {
  const DEFAULT_SEGMENT_TERMINATOR      = "\n";
  const DEFAULT_FIELD_SEPARATOR         = "|";
  const DEFAULT_COMPONENT_SEPARATOR     = "^";
  const DEFAULT_REPETITION_SEPARATOR    = "~";
  const DEFAULT_ESCAPE_CHARACTER        = "\\";
  const DEFAULT_SUBCOMPONENT_TERMINATOR = "&";

  static $enteredHeaders = array("MSH", "FHS", "BHS");
   
  var $segmentTerminator     = self::DEFAULT_SEGMENT_TERMINATOR;
  var $fieldSeparator        = self::DEFAULT_FIELD_SEPARATOR;
  var $componentSeparator    = self::DEFAULT_COMPONENT_SEPARATOR;
  var $repetitionSeparator   = self::DEFAULT_REPETITION_SEPARATOR;
  var $escapeCharacter       = self::DEFAULT_ESCAPE_CHARACTER;
  var $subcomponentSeparator = self::DEFAULT_SUBCOMPONENT_TERMINATOR;

  var $version     = '2.5';
  var $name        = null;
  var $description = null;
  var $segments    = array();
  var $lines       = array();
  
  function parse($data) {
    parent::parse($data);
		
		$message = $this->data;
		
    // first tokenize the segments
    if (($message == null) || (strlen($message) < 4)) {
      throw new CHL7v2Exception($message, CHL7v2Exception::EMPTY_MESSAGE);
    }
    
    $this->fieldSeparator = $message[3];
		
    // valid separator
    if (!preg_match("/[^a-z0-9]/i", $this->fieldSeparator) ) {
      throw new CHL7v2Exception($message, CHL7v2Exception::INVALID_SEPARATOR);
    }

    $nextDelimiter = strpos($message, $this->fieldSeparator, 4);
    if ($nextDelimiter > 4) {
      // usually ^
      $this->componentSeparator = $message[4];
    }
    if ($nextDelimiter > 5) {
      // usually ~
      $this->repetitionSeparator = $message[5];
    }
    if ($nextDelimiter > 6) {
      // usually \
      $this->escapeCharacter = $message[6];
    }
    if ($nextDelimiter > 7) {
      // usually &
      $this->subcomponentSeparator = $message[7];
    }

    // replace the special case of ^~& with ^~\&
    if ("^~&|" == substr($message, 4, 4)) {
      $this->escapeCharacter       = "\\";
      $this->subcomponentSeparator = "&";
      $this->repetitionSeparator   = "~";
      $this->componentSeparator    = "^";
    }
    
    $this->readHeader();
    $this->readSegments();
  }
	
	function readHeader(){
    $this->lines = explode($this->segmentTerminator, $this->data);
		
		$first_line = array_shift($this->lines);
		
    $segment = new CHL7v2Segment($this);
    $segment->parse($first_line);
		$this->segments[0] = $segment;
		
    $this->name    = $segment->fields[7 ]->items[0]->components[2];
    $this->version = $segment->fields[10]->items[0]->components[0];
	}
  
	// @todo Gérer les segments recursif s (pour le moment tout est aplati)
  function readSegments() {
  	$specs = $this->getSpecs();
		$segments_spec = $specs->xpath("//segment");
		$i_line = 0;
		$current_line_segment = null;
		
		foreach($segments_spec as $i_spec => $_segment_spec) {
      $name = (string)$_segment_spec;
			$segment = null;
			
			if($name == "MSH") continue;
			
			if (!$current_line_segment && isset($this->lines[$i_line])) {
			  $current_line_segment = new CHL7v2Segment($this);
        $current_line_segment->parse($this->lines[$i_line++]);
			}
			
      if ($current_line_segment && $name == $current_line_segment->name) {
      	$segment = $current_line_segment;
				$current_line_segment = null;
      }
			
			/* // necessite la gestion des groupes de segments
			$minOccurs = (string)($_segment_spec->attributes()->minOccurs);
			if ($minOccurs != "0" && !$segment) {
				throw new CHL7v2Exception("Segment absent : $name");
			}*/
			
			if (isset($segment)) {
			  $this->segments[$i_spec] = $segment;
			}
		}
		
    /*foreach ($this->lines as $_line) {
      try {
        $segment = new CHL7v2Segment($this);
        $segment->parse($_line);
      } catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
    }
    
    $this->validate();*/
  }
  
  function validate() {
  	/*$specs = $this->getSpecs();
		
		mbtrace($specs);*/
    /*$segments = array();
		
		$i = 0;
    foreach($specs->segments as $_segment) {
    	if ($_segment->attributes()->minOccurs == 0) {
    		mbTrace($_segment);
    	}
      if (!isset($this->segments[$i])) {
        //mbTrace($i, $this->owner_segment->name);
        break;
      }
      $parts[(string)$_field->name] = $this->parts[$i];
      $i++;
    }*/
		
    // validate all segments
    foreach ($this->segments as $_segment) {
      try {
        $_segment->validate();
      } catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
    }
  }
  
  function getVersion(){
    return $this->version;
  }
	
	function getSpecs(){
		return $this->getSchema(self::PREFIX_MESSAGE_NAME, $this->name);
	}
	
	/*
	function __toString(){
		$s = "<h2>$this->name - $this->description</h2>";
		$s .= implode("<br />", $this->segments);
		return $s;
	}*/
}

?>