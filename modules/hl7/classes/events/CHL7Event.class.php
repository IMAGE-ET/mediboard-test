<?php

/**
 * Event HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7Event 
 * Event HL7
 */
class CHL7Event {

  /** @var null */
  public $event_type;

  /** @var null */
  public $object;

  /** @var CUserLog */
  public $last_log;


  /** @var string */
  public $version;
  
  /**
   * @var CReceiverHL7v2|CReceiverHL7v3
   */
  public $_receiver;
  

  /** @var CInteropSender */
  public $_sender;


  /** @var CExchangeDataFormat */
  public $_data_format;

  /**
   * Construct
   *
   * @return CHL7Event
   */
  function __construct() {
  }
  
  /**
   * Build HL7 message
   *
   * @param CMbObject $object Object to use
   *
   * @return void
   */
  function build($object) {
  }

  /**
   * Handle event
   *
   * @return void
   */
  function handle() {
  }
}