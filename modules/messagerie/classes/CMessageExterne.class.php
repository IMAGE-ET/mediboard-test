<?php

/**
 * $Id$
 *  
 * @category Messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CMessageExterne extends CMbObject {

  //mail_object, bioserveur_object_file, ...

  public $account_id;     // account ID

  //behaviour
  public $archived;       //bool
  public $starred;        //bool
  public $date_read;      //dateTime
  public $date_received;  //date of mb received
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["account_id"]    = "ref class|CMbObject notNull";
    $props["archived"]      = "bool notNull default|0";
    $props["starred"]       = "bool notNull default|0";
    $props["date_read"]     = "dateTime";
    $props["date_received"] = "dateTime";
    
    return $props;
  }
}