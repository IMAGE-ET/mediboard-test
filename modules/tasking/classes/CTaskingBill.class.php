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
class CTaskingBill extends CMbObject {
  /** @var  integer Primary key */
  public $tasking_bill_id;

  /** @var  string Bill filename */
  public $bill_name;

  /** @var  CTaskingTicket[] Tasks billed */
  public $_ref_tasking_tickets;

  /**
   * Load all the tasks of a CTaskingBill
   *
   * @return CTaskingTicketMessage[]
   */
  function loadRefsMessages() {
    return $this->_ref_tasking_messages = $this->loadBackRefs("tasking_ticket_messages");
  }

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "tasking_bill";
    $spec->key    = "tasking_bill_id";
    return $spec;  
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["tasking_tickets"] = "CTaskingTicket bill_id";

    return $backProps;
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["bill_name"] = "str notNull";
    
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields () {
    parent::updateFormFields();

    $this->_view = $this->bill_name;
  }
}
