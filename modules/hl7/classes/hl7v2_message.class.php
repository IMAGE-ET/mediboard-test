<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "hl7v2_segment_group");

class CHL7v2Message extends CHL7v2SegmentGroup {
  const DEFAULT_ESCAPE_CHARACTER        = "\\";
  const DEFAULT_SEGMENT_TERMINATOR      = "\n";
  const DEFAULT_FIELD_SEPARATOR         = "|";
  const DEFAULT_COMPONENT_SEPARATOR     = "^";
  const DEFAULT_REPETITION_SEPARATOR    = "~";
  const DEFAULT_SUBCOMPONENT_TERMINATOR = "&";

  static $enteredHeaders = array("MSH", "FHS", "BHS");
   
  var $escapeCharacter       = self::DEFAULT_ESCAPE_CHARACTER;
  var $segmentTerminator     = self::DEFAULT_SEGMENT_TERMINATOR;
  var $fieldSeparator        = self::DEFAULT_FIELD_SEPARATOR;
  var $componentSeparator    = self::DEFAULT_COMPONENT_SEPARATOR;
  var $repetitionSeparator   = self::DEFAULT_REPETITION_SEPARATOR;
  var $subcomponentSeparator = self::DEFAULT_SUBCOMPONENT_TERMINATOR;

  var $version     = '2.5';
  var $name        = null;
  var $description = null;
  var $lines       = array();
	var $current_line = 0;
    
  function __construct() {
    //
  }
  
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
		
		$first_line = $this->lines[0];
		$this->current_line++;
		
    $segment = new CHL7v2Segment($this);
    $segment->parse($first_line);
		$this->appendChild($segment);
		
    $this->name    = (isset($segment->fields[8 ]->items[0]->components[2]) ? $segment->fields[8 ]->items[0]->components[2] : $segment->fields[8 ]->items[0]->components[0]."_".$segment->fields[8 ]->items[0]->components[1]);
    $this->version = $segment->fields[11]->items[0]->components[0];
	}
	
	function getCurrentLineHeader(){
		return substr($this->getCurrentLine(), 0, 3);
	}
  
  function getCurrentLine(){
    return $this->lines[$this->current_line];
  }
	
	function handleSpecItem($child_spec) {
    $type = $child_spec->getName();
    $minOccurs = $child_spec->attributes()->minOccurs;
    
    switch($type) {
      case "segment": 
        if ($this->getCurrentLineHeader() == (string)$child_spec) {
          $_segment = new CHL7v2Segment($this);
          $_segment->parse($this->getCurrentLine());
          $this->appendChild($_segment);
          $this->current_line++;
          break;
        }
        
        if($minOccurs != "0") {
          throw new CHL7v2Exception("Segment missing $child_spec");
        }
        
        break;
      case "group": 
        $stack = CHL7v2SegmentGroup::getFirstSegmentHeader($child_spec, $this->getCurrentLineHeader());
        
        if (empty($stack) && $minOccurs != "0") {
          throw new CHL7v2Exception("Segment missing $child_spec");
        }
        
        foreach($stack as $_element) {
        	$this->handleSpecItem($_element);
					return;
          mbTrace($_element->getName());
          mbTrace();
        }
        
        //mbTrace($stack);
        /*if ($this->getCurrentLineHeader() == (string)$first_segment) {
          mbTrace($first_segment);
        }*/
        // :'(
    }
	}
  
	// @todo Gérer les segments recursif s (pour le moment tout est aplati)
  function readSegments() {
  	$specs = $this->getSpecs();
		
		/**
		 * On parcourt la spec, qui est une sequence de segments et de groupes de segments ou de groupes (recursif).
		 * Si c'est un segment, on regarde s'il correspond au header de 3 lettres de la ligne courante : 
		 *   * si oui on le lit
		 *   * si non et que :
		 *      * il est obligatoire : erreur, 
		 *      * sinon on passe à la spec du segment suivant
		 *   
		 * Si c'est un groupe, ça se corse : il faut regarder si un des segments à l'interieur correspond 
		 * au header de 3 lettres de la ligne en cours : 
		 *   * si on en trouve un, c'est qu'il faut "entrer" dans ce groupe 
		 *      (pb avec cette methode : ca valide mal si un des segments ou groupes obligatoires precede ce segment)
		 *   * si non et que :
		 *      - le groupe est obligatoire : erreur
		 *      - le groupe n'est pas obligatoire : spec du groupe suivant
		 *      
		 * methode plus efficace pour les groupes : 
		 * on a une pile de groupes, dans laquelle on push les groupes dans lesquels on rentre et pop ceux dont on sort.
		 * Dans tous les cas quand on sort d'un groupe obligatoire sans avoir trouvé un segment : erreur.
		 * Quand on est dans une spec de groupe, on ne sort pas tant qu'on n'a pas parcouru tous ses segments.
		 * 
		 */
		
		/**
		 * @var CHL7v2SimpleXMLElement
		 */
		$current_node = next($specs->xpath("/message/segments/*"));
		
		/**
		 * @var CHL7v2SegmentGroup
		 */
		$current_group = $this;
		
		$n = 100;
		try {
			while($n-- && $current_node) {
		    switch($current_node->getName()) {
		    	
		    	// SEGMENT //
		      case "segment":
						mBtrace($current_node->getSegmentHeader(), "Segment");
						
						// Si la spec correspond a la ligne courante
		        if ($this->getCurrentLineHeader() == $current_node->getSegmentHeader()) {
		        	// On est dans le bon groupe
		        	$current_node->markOpen();
							
							// On enregistre le segment dans le groupe courant
		          $_segment = new CHL7v2Segment($current_group);
		          $_segment->parse($this->getCurrentLine());
		          $current_group->appendChild($_segment);
							
							// On avance dans le fichier
		          $this->current_line++;
		        }
		        
						// Si le segment est requis, alors erreur
		        elseif ($current_node->open && $current_node->isRequired()) {
		          throw new CHL7v2Exception("Segment missing $current_node");
		        }
         
				    // increment d'utilisation
            if ($current_node->isUnbounded()) {
              mBtrace("Segment incrémenté");
            	$current_node->occurences++;
            }
						
				    else {
              mBtrace("Segment unique");
					    // Segment/groupe suivant ou suivant du parent
	            $current_node = $current_node->getNext();
				    }
		        break;
						
						
          // GROUP //
		      case "group":
            mBtrace("Groupe");
		        $current_node = $current_node->getFirstChild();
						break;
						
						// custom attributes
					default: 
            mbTrace($current_node->getName()); 
					  $current_node = $current_node->getNextSibling();
						break;
		    }
			}
		}
		catch (CHL7v2FinishedException $e) {
			// Yay !
		}
    
    // pas forcément utile : mais ceci donne tous les segments dans 
    // l'ordre de parcours, comme si on le faisait recursivement
    // $c = $specs->xpath("//segment | //group");
    
    // descendants directs du message
    /*$children = $specs->xpath("/message/segments/*");
    array_shift($children); // on passe le segment MSH
    
    foreach($children as $child_spec) {
      $this->handleSpecItem($child_spec);
    }*/
		
		/*
		$segments_spec = $specs->xpath("//segment");
		$i_line = 0;
		$current_line_segment = null;
		
		foreach($segments_spec as $i_spec => $_segment_spec) {
      $name = (string)$_segment_spec;
			$segment = null;
			
			if($name == "MSH") continue;
			
			if (!$current_line_segment && isset($this->lines[$i_line])) {
        try {
			    $current_line_segment = new CHL7v2Segment($this);
          $current_line_segment->parse($this->lines[$i_line++]);
	      } catch (Exception $e) {
	        exceptionHandler($e);
	        return;
	      }
			}
			
      if ($current_line_segment && $name == $current_line_segment->name) {
      	$segment = $current_line_segment;
				$current_line_segment = null;
      }
			
			 // necessite la gestion des groupes de segments
			//$minOccurs = (string)($_segment_spec->attributes()->minOccurs);
			//if ($minOccurs != "0" && !$segment) {
			//	throw new CHL7v2Exception("Segment absent : $name");
			//}
			
			if (isset($segment)) {
			  $this->segments[$i_spec] = $segment;
			}
		}*/
		
		/*foreach($this->lines as $i => $_line) {
	    try {
        $_segment = new CHL7v2Segment($this);
        $_segment->parse($_line);
      } catch (Exception $e) {
        exceptionHandler($e);
        return;
      }
			
			$this->segments[] = $_segment;
    }*/
  }
	
	function readGroup($spec, &$i) {
		
	}
  
  function validate() {
  	// Validation faite lors du readSegments
  }
  
  function getVersion(){
    return $this->version;
  }
	
	function getSpecs(){
		return $this->getSchema(self::PREFIX_MESSAGE_NAME, $this->name);
	}
  
  function getMessage() {
    return $this;
  }
}

?>