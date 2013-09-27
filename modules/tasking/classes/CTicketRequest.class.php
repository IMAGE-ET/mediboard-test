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
class CTicketRequest extends CMbMetaObject {
  /** @var  integer Primary key */
  public $ticket_request_id;

  /** @var  string CTicketRequest label */
  public $label;

  /** @var  string CTicketRequest description */
  public $description;

  /** @var  integer CTicketRequest priority */
  public $priority;

  /** @var  datetime CTicketRequest due date */
  public $due_date;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "ticket_request";
    $spec->key    = "ticket_request_id";
    return $spec;  
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_class"] = "enum list|CMonitorGroup|CMonitorFunction|CMonitorSite|CTaskingContactEvent|CMediusers notNull";
    $props['label']        = 'str notNull';
    $props['description']  = 'text';
    $props['priority']     = 'num';
    $props['due_date']     = 'dateTime';
    
    return $props;
  }
}
