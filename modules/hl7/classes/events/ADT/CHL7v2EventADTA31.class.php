<?php

/**
 * A31 - Update person information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA31 
 * A31 - Add person information
 */
class CHL7v2EventADTA31 extends CHL7v2EventADT implements CHL7EventADTA05 {
  /**
   * @var string
   */
  public $code        = "A31";
  /**
   * @var string
   */
  public $struct_code = "A05";

  /**
   * Build A31 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient){
    parent::build($patient);
    
    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Doctors
    $this->addROLs($patient);
    
    // Next of Kin / Associated Parties
    $this->addNK1s($patient);
    
    // Patient Visit
    $this->addPV1();    
  }
}