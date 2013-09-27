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
class CTaskingContactEvent extends CMbObject {
  /** @var  integer Primary key */
  public $tasking_contact_event_id;

  /** @var  datetime Date from where the event started */
  public $date_start;

  /** @var  datetime End date of the event */
  public $date_end;

  /** @var  integer CTaskingContact reference */
  public $tasking_contact_id;

  /** @var  integer CMediusers reference */
  public $interlocutor_user_id;

  /** @var  string Event description */
  public $event_description;

  /** @var  string Event comment */
  public $event_comment;

  /** @var  integer Event duration */
  public $_duration;

  /** @var  integer CMonitorSite reference */
  public $site_id;

  /** @var  integer Subject object reference */
  public $subject_id;

  /** @var  string Subject object class */
  public $subject_class;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "tasking_contact_event";
    $spec->key    = "tasking_contact_event_id";
    return $spec;  
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_duration = CMbDT::minutesRelative($this->date_start, $this->date_end);
    //$this->_view     = "$this->customer => $this->interlocutor [$this->date_start]";
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
    $props['date_start']           = "dateTime notNull";
    $props['date_end']             = "dateTime notNull";
    $props['tasking_contact_id']   = "ref notNull class|CTaskingContact";
    $props['interlocutor_user_id'] = "ref notNull class|CMediusers";
    $props['event_description']    = "text helped seekable";
    $props['event_comment']        = "text helped seekable";
    $props['_duration']            = "num";
    $props['site_id']              = "ref notNull class|CMonitorSite";
    $props['subject_id']           = "ref class|CMbObject meta|subject_class";
    $props['subject_class']        = "enum list|CMonitorGroup|CMonitorSite|CMonitorFunction show|0";

    return $props;
  }
}
