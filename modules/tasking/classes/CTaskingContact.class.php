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
class CTaskingContact extends CMbObject {
  /** @var  integer Primary key */
  public $tasking_contact_id;

  /** @var  string Contact first name */
  public $first_name;

  /** @var  string Contact last name */
  public $last_name;

  /** @var  string Contact email address */
  public $email;

  /** @var  string Contact mobile phone */
  public $mobile_phone;

  /** @var  string Contact desk phone */
  public $desk_phone;

  /** @var  integer CMonitorSite ID */
  public $site_id;

  /** @var  CTaskingContactEvent[] CTaskingContactEvent references */
  public $_ref_tasking_contact_events;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "tasking_contact";
    $spec->key    = "tasking_contact_id";
    return $spec;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['tasking_contact_events'] = "CTaskingContactEvent tasking_contact_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['first_name']   = "str";
    $props['last_name']    = "str notNull";
    $props['email']        = "str";
    $props['mobile_phone'] = "str";
    $props['desk_phone']   = "str";
    $props['site_id']      = "ref notNull class|CMonitorSite";
    
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = "$this->first_name $this->last_name";
  }

  /**
   * Load all the contact events of a CTaskingContact
   *
   * @return CTaskingContactEvent[]
   */
  function loadRefsContactEvents() {
    return $this->_ref_tasking_contact_events = $this->loadBackRefs("tasking_contact_events");
  }
}
