<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "CHL7v2SegmentGroup");

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
    $data = trim($data);
    
    parent::parse($data);
    
    $message = $this->data;
    
    // first tokenize the segments
    if (($message == null) || (strlen($message) < 4)) {
      throw new CHL7v2Exception(CHL7v2Exception::EMPTY_MESSAGE, $message);
    }
    
    $this->fieldSeparator = $message[3];
    
    // valid separator
    if (!preg_match("/[^a-z0-9]/i", $this->fieldSeparator) ) {
      throw new CHL7v2Exception(CHL7v2Exception::INVALID_SEPARATOR, $message);
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
    
    $this->lines = CHL7v2::split($this->segmentTerminator, $this->data);
    
    // we extract the first line info "by hand"
    $first_line = CHL7v2::split($this->fieldSeparator, reset($this->lines));
    
    // version
    preg_match('/^([\d\._]+)/', $first_line[11], $version_matches);
    $this->version = $version_matches[1];
    
    // message type
    $message_type = explode($this->componentSeparator, $first_line[8]);
    if (isset($message_type[2])) {
      $type = $message_type[2];
    }
    else {
      $type = implode("", $message_type);
    }
    $this->name    = preg_replace("/[^A-Z0-9]/", "", $type);
    
    $this->validate();
    
    $this->readHeader();
    $this->readSegments();
  }
  
  function readHeader(){
    $first_line = $this->lines[0];
    $this->current_line++;
    
    // segment from line's string
    $segment = new CHL7v2Segment($this);
    $segment->parse($first_line);
    
    // this one will be the first segment
    $this->appendChild($segment);
  }
  
  function getCurrentLineHeader(){
    return substr($this->getCurrentLine(), 0, 3);
  }
  
  function getCurrentLine($offset = 0){
    return CValue::read($this->lines, $this->current_line+$offset, null);
  }
  
  static function getNext($current_node, $current_group) {
    // On remet les compteurs d'utilisation a zero
    $current_node->reset();
    
    $next = $current_node->getNextSibling();
    if ($next) {
      CHL7v2::d(" --> Suivant = frere");
      $current_node = $next;
    }
    else {
      CHL7v2::d(" --> Suivant = suivant du parent");
      $parent = $current_node->getParent();
      
      if (!$parent->isUnbounded() || $parent->getOccurences() == 0) {
        CHL7v2::d(" --> Suivant = suivant du parent");
        return self::getNext($parent, $current_group->parent);
      }
      
      CHL7v2::d(" --> Suivant = parent");
      $current_node = $current_node->getParent();
      $current_group = $current_group->parent;
    }
    
    return array($current_node, $current_group);
  }
  
  // @todo Gérer les segments recursif s (pour le moment tout est aplati)
  function readSegments() {
    $specs = $this->getSpecs();
    
    /**
     * Premier segment/groupe dans le fichier de spec venant de Mirth
     * 
     * @var CHL7v2SimpleXMLElement
     */
    $current_node = next($specs->xpath("/message/segments/*"));
    
    /**
     * Groupe courant dans lequel on va placer les CHL7v2Segment créés
     * 
     * @var CHL7v2SegmentGroup
     */
    $current_group = $this;
    
    $lines_count = count($this->lines);
    
    $n = 500; // pour eviter les boucles infinies !

    while($n-- && $current_node && $this->current_line < $lines_count) {
      switch($current_node->getName()) {
        
        // SEGMENT //
        case "segment":
          CHL7v2::d($current_node->getSegmentHeader(), "Segment");
          
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
            CHL7v2::d(" --> ### Creation du segment ###, ligne suivante : $this->current_line");
          }
          
          // Segment non requis, on passe au suivant
          elseif(!$current_node->isRequired()) {
            CHL7v2::d(" --> Segment non présent et non requis");
            list($current_node, $current_group) = self::getNext($current_node, $current_group);
            break;
          }
          
          // Si le segment est requis et que le groupe est ouvert, alors erreur
          // pas de parent si à la racine (fils de <segments>) : bizarre 
          else {
            if (!$current_node->getParent() || $current_node->getParent()->isOpen()) {
              CHL7v2::d(" --> !!!!!!!!!!!!!!!!! Segment non present et groupe requis");
              throw new CHL7v2Exception(CHL7v2Exception::SEGMENT_MISSING, (string)$current_node);
            }
          }
       
          // le segment est multiple
          if ($current_node->isUnbounded()) {
            CHL7v2::d(" --> Segment multiple");
          }
          
          // Segment unique : Segment/groupe suivant ou suivant du parent
          else {
            CHL7v2::d(" --> Segment unique, passage au suivant");
            list($current_node, $current_group) = self::getNext($current_node, $current_group);
          }
          break;
          
          
        // GROUP //
        case "group":
          CHL7v2::d((string)$current_node->attributes()->name);
          
          if ($current_node->isUnbounded() || $current_node->getOccurences() == 0) {
            CHL7v2::d(" --> Groupe multiple ou pas encore utilisé, on entre dedans (occurences = ".$current_node->getOccurences().")");
            $current_group = new CHL7v2SegmentGroup($current_group);
            $current_group->name = (string)$current_node->attributes()->name;
            
            $current_node = $current_node->getFirstChild();
          }
          else {
            CHL7v2::d(" --> Groupe utilisé ou pas multiple, on prend le parent ou frere (occurences = ".$current_node->getOccurences().")");
            list($current_node, $current_group) = self::getNext($current_node, $current_group);
          }
          
          break;
          
          // custom attributes, should never get there
        default: 
          CHL7v2::d($current_node->getName()); 
          $current_node = $current_node->getNextSibling();
          break;
      }
    }
    
    // pas forcément utile : mais ceci donne tous les segments dans 
    // l'ordre de parcours, comme si on le faisait recursivement
    // $c = $specs->xpath("//segment | //group");
  }
  
  function validate() {
    // validation de la syntaxe : chaque ligne doit commencer par 3 lettre + un separateur + au moins une donnée
    $sep_preg = preg_quote($this->fieldSeparator);
    
    foreach($this->lines as $line) {
      if (!preg_match("/^[A-Z0-9]{3}$sep_preg.+/", $line)) {
        throw new CHL7v2Exception(CHL7v2Exception::SEGMENT_INVALID_SYNTAX, $this->fieldSeparator, $line);
      }
    }
  }
  
  function getVersion(){
    return $this->version;
  }
  
  function getSpecs(){
    return $this->getSchema(self::PREFIX_MESSAGE_NAME, $this->name);
  }
  
  /**
   * @return CHL7v2Message
   */
  function getMessage() {
    return $this;
  }
}
