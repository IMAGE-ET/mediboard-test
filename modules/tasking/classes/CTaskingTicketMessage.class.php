<?php

/**
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CTaskingTicketMessage extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $tasking_ticket_message_id;

  /** @var  string Ticket message title */
  public $title;

  /** @var  string Ticket message text */
  public $text;

  /** @var  datetime Ticket message creation datetime */
  public $creation_date;

  /** @var  integer Ticket message CTaskingTicket ID */
  public $task_id;

  /** @var  integer Ticket message CMediusers ID */
  public $user_id;

  /** @var  string Ticket message author view */
  public $_user_view;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "tasking_ticket_message";
    $spec->key    = "tasking_ticket_message_id";
    return $spec;  
  }
  
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
    $props['title']         = "str";
    $props['text']          = "text notNull";
    $props['creation_date'] = "dateTime notNull";
    $props['task_id']       = "ref class|CTaskingTicket cascade notNull";
    $props['user_id']       = "ref class|CMediusers";
    
    return $props;
  }

  /**
   * Load the author's _view
   *
   * @return string
   */
  function loadAuthorView() {
    if ($this->user_id) {
      $user = new CMediusers();
      $user->load($this->user_id);

      $this->_user_view = $user->_view;
    }

    return $this->_user_view;
  }
}
