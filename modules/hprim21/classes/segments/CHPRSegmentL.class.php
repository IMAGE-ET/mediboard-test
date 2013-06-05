<?php

/**
 * Represents an HPR L message segment (Message Footer) - HPR
 *  
 * @category Hprim21
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License; see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPRSegmentL
 * L - Represents an HPR L message segment (Message Footer)
 */

class CHPRSegmentL extends CHL7v2Segment {
  public $name = "L";

  /**
   * @see parent::build
   */
  function build(CHPREvent $event) {
    parent::build($event);
        
    $data = array();

    // L-1 : Segment Row (optional)
    $data[] = null;
    
    // L-2 : Not Use (optional)
    $data[] = null;
    
    // L-3 : Number Segment P (optional)
    $data[] = null;
    
    // L-4 : Number Segment of Message (optional)
    $data[] = null;
    
    // L-5 : Lot Number (optional)
    $data[] = null;
    
    $this->fill($data);
  }
}
