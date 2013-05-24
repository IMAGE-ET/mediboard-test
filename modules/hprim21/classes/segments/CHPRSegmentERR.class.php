<?php

/**
 * Represents an HPR ERR message segment (Error) - HPR
 *  
 * @category hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPRSegmentERR 
 * ERR - Represents an HPR ERR message segment (Error)
 */

class CHPRSegmentERR extends CHL7v2Segment {
  var $name = "ERR";
  
  /**
   * @var CHPrim21Acknowledgment
   */
  public $acknowledgment;
  
  /**
   * @var CHL7v2Error
   */
  public $error;
  
  function build(CHPREvent $event) {
    parent::build($event);
    
    $error          = $this->error;
    $acknowledgment = $this->acknowledgment;
    $exchange_hpr   = $event->_exchange_hpr;

    $data = array();
    
    if ($error instanceof CHL7v2Error) {
      // ERR-1: Segment Row
      $data[] = $acknowledgment->_row;
      
      // ERR-2: Filename
      $data[] = $exchange_hpr->nom_fichier;
      
      // ERR-3: Date / Time of receipt
      $data[] = $exchange_hpr->date_production;
      
      // ERR-4: Severity
      $data[] = null;
      
      // ERR-5: Line number
      $data[] = null;
  
      // ERR-6: Error Location
      $data[] = null;
      
      // ERR-7: Field Position
      $data[] = null;
      
      // ERR-8: Error value
      $data[] = null;
      
      // ERR-9: Error type
      $data[] = null;
      
      // ERR-10: Original Text
      $data[] = null;
    }
    else {
      // ERR-1
      $data[] = $acknowledgment->_row;
      
      // ERR-2
      $data[] = $exchange_hpr->nom_fichier;
      
      // ERR-3
      $data[] = $exchange_hpr->date_production;
      
      // ERR-4
      $data[] = $error[0];
      
      // ERR-5
      $data[] = null;
      
      // ERR-6
      $data[] = array( 
        array(
          $error[2][0],
          $error[2][1],
          $error[2][2]
        )
      );
      
      // ERR-7
      $data[] = null;
      
      // ERR-8
      $data[] = $error[4];
      
      // ERR-9
      $data[] = $error[5];
      
      // ERR-10
      $data[] = $error[6];
    }
    
    
    $this->fill($data);
  }
}
