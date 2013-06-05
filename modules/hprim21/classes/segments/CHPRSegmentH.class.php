<?php

/**
 * Represents an HPR H message segment (Message Header) - HPR
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPRSegmentH
 * H - Represents an HPR L message segment (Message Header)
 */

class CHPRSegmentH extends CHL7v2Segment {
  public $name = "H";

  /**
   * @see parent::build
   */
  function build(CHPREvent $event) {
    parent::build($event);
    
    $message  = $event->message;
    
    $data = array();
        
    // H-1 : Field Separator
    $data[] = $message->fieldSeparator;
    
    // H-2: Encoding Characters (ST)
    $data[] = substr($message->getEncodingCharacters(), 1);       
    
    // H-3 : Message ID
    $data[] = $event->_exchange_hpr->_id; 
    
    // H-4 : Password
    $data[] = null;
    
    // H-5 : Sender ID
    $data[] = $event->_sender->nom;
    
    // H-6 : Sender address
    $data[] = null;
    
    // H-7 : Context
    $data[] = $event->event_type;
    
    // H-8 : Sender phone
    $data[] = null;
    
    // H-9 : Transmission characteristics
    $data[] = null;
    
    // H-10 : Receiver ID
    $data[] = array(
      $event->_receiver->_id,
      $event->_receiver->nom
    );
    
    // H-11 : Comment
    $data[] = null;
    
    // H-12 : Processing ID
    $data[] = null;
    
    // H-13 : Version and Type
    $data[] = array(
      array(
        $event->version,
        $event->_exchange_hpr->type
      )
    );
    
    // H-14 : Date/Time of Message
    $data[] = CMbDT::dateTime();

    $this->fill($data);
  }
}
