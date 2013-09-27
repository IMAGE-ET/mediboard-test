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
class CTaskingTicket extends CMbObject {
  /** @var  integer Primary key */
  public $tasking_ticket_id;

  /** @var  string Ticket description */
  public $ticket_name;

  /** @var  datetime Ticket creation date */
  public $creation_date;

  /** @var  datetime Ticket last modification date */
  public $last_modification_date;

  /** @var  datetime Ticket due date */
  public $due_date;

  /** @var  datetime Ticket closing date */
  public $closing_date;

  /** @var  integer Ticket priority level */
  public $priority;

  /** @var  integer Ticket complete time estimation, in hours */
  public $estimate;

  /** @var  string Ticket type */
  public $type;

  /** @var  string Ticket status */
  public $status;

  /** @var  string Ticket funding */
  public $funding;

  /** @var  integer Ticket postponements number */
  public $nb_postponements;

  /** @var  integer CMediusers ID of the user to whom the ticket has been assigned */
  public $assigned_to_id;

  /** @var  integer CMediusers ID of the user which supervises the ticket */
  public $supervisor_id;

  /** @var  integer CTaskingTicket ID of the duplicate */
  public $duplicate_of_id;

  /** @var  integer CTaskingBill ID of the bill */
  public $bill_id;

  /** @var  CTagItem[] Ticket bills */
  public $_ref_bills;

  /** @var  CTaskingTicketMessage[] Ticket CTaskingTicketMessage */
  public $_ref_tasking_messages;

  /** @var  CTag[] Ticket CTag */
  public $_ref_tags;

  /** @var  CMediusers Ticket CMediusers */
  public $_ref_assigned_to_user;

  /** @var  CMediusers Ticket CMediusers */
  public $_ref_supervisor_user;

  /** @var  CTaskingTicket Ticket duplicate of */
  public $_ref_duplicate_of;

  /** @var  CTaskingTicket Ticket CTaskingBill */
  public $_ref_bill;

  /** @var  array Ticket status */
  public $_status;

  /** @var  array Ticket type */
  public $_type;

  /** @var  array Ticket priority */
  public $_priority;

  /** @var  array Ticket funding */
  public $_funding;

  /** @var  datetime Ticket creation date min. interval */
  public $_creation_date_min;

  /** @var  datetime Ticket creation date max. interval */
  public $_creation_date_max;

  /** @var  datetime Ticket due date min. interval */
  public $_due_date_min;

  /** @var  datetime Ticket due date max. interval */
  public $_due_date_max;

  /** @var  datetime Ticket closing date min. interval */
  public $_closing_date_min;

  /** @var  datetime Ticket closing date max. interval */
  public $_closing_date_max;

  /** @var  string Ticket due date status */
  public $_due_date;

  /** @var  string Ticket status resume CSS class */
  public $_status_resume;

  /** @var  string Needed if we have to duplicate the tasking ticket */
  public $_duplicate;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "tasking_ticket";
    $spec->key    = "tasking_ticket_id";
    return $spec;  
  }
  
  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['tasking_ticket_messages'] = "CTaskingTicketMessage task_id";
    $backProps['tasking_ticket_tags']     = "CTagItem object_id";

    return $backProps;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props['ticket_name']            = 'str notNull';
    $props['creation_date']          = 'dateTime notNull';
    $props['last_modification_date'] = 'dateTime';
    $props['due_date']               = 'dateTime';
    $props['closing_date']           = 'dateTime';
    $props['priority']               = 'enum list|0|1|2|3 default|0 notNull';
    $props['estimate']               = 'num';
    $props['type']                   = 'enum list|ref|bug|erg|fnc|action default|ref notNull';
    $props['status']                 = 'enum list|new|accepted|inprogress|invalid|duplicate|cancelled|closed|refused notNull';
    $props['funding']                = 'enum list|fund-cus|fund-50|fund-ox';
    $props['nb_postponements']       = 'num default|0';
    $props['assigned_to_id']         = 'ref class|CMediusers';
    $props['supervisor_id']          = 'ref class|CMediusers';
    $props['duplicate_of_id']        = 'ref class|CTaskingTicket';
    $props['bill_id']                = 'ref class|CTaskingBill autocomplete|bill_name';

    $props['_status']                = 'set list|new|accepted|inprogress|invalid|duplicate|cancelled|closed|refused';
    $props['_type']                  = 'set list|ref|bug|erg|fnc|action default|ref';
    $props['_priority']              = 'set list|0|1|2|3';
    $props['_funding']               = 'set list|fund-cus|fund-50|fund-ox|fund-no';

    $props['_creation_date_min']     = 'dateTime';
    $props['_creation_date_max']     = 'dateTime';
    $props['_due_date_min']          = 'dateTime';
    $props['_due_date_max']          = 'dateTime';
    $props['_closing_date_min']      = 'dateTime';
    $props['_closing_date_max']      = 'dateTime';

    $props['_due_date']              = 'str';
    $props['_duplicate']             = 'str';
    $props['_status_resume']         = 'str';

    return $props;
  }

  /**
   * Load all the ticket messages of a CTaskingTicket
   *
   * @return CTaskingTicketMessage[]
   */
  function loadRefsMessages() {
    return $this->_ref_tasking_messages = $this->loadBackRefs("tasking_ticket_messages", "creation_date DESC");
  }

  /**
   * Load all the tags of a CTaskingTicket
   *
   * @return CTag[]
   */
  function loadRefsTags() {
    foreach ($this->loadBackRefs("tasking_ticket_tags") as $_tag_item) {
      $this->_ref_tags[] = $_tag_item->loadRefTag();
    }
    return $this->_ref_tags;
  }

  /**
   * Get CMediusers
   *
   * @return CMediusers
   */
  function loadRefAssignedToUser() {
    return $this->_ref_assigned_to_user = $this->loadFwdRef("assigned_to_id");
  }

  /**
   * Get CMediusers
   *
   * @return CMediusers
   */
  function loadRefSupervisorUser() {
    return $this->_ref_supervisor_user = $this->loadFwdRef("supervisor_id");
  }

  /**
   * Get CTaskingTicket
   *
   * @return CTaskingTicket
   */
  function loadRefDuplicateOf() {
    return $this->_ref_duplicate_of = $this->loadFwdRef("duplicate_of_id");
  }

  /**
   * Get CTaskingBill
   *
   * @return CTaskingBill
   */
  function loadRefBill() {
    return $this->_ref_bill = $this->loadFwdRef("bill_id");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $due_date = CMbDT::date($this->due_date);
    $today    = CMbDT::date();

    if ($due_date == $today) {
      $this->_due_date = "due_today";
    }
    elseif ($due_date > $today) {
      $this->_due_date = "upcoming";
    }
    elseif ($due_date < $today) {
      $this->_due_date = "overdue";
    }

    switch ($this->status) {
      case "invalid":
      case "duplicate":
      case "cancelled":
      case "closed":
      case "refused":
        $this->_status_resume = "task-completed";
    }
  }

  /**
   * @see parent::store()
   */
  function store() {
    if ($this->fieldModified("assigned_to_id")) {
      $this->status = "new";
    }

    if ($msg = parent::store()) {
      return $msg;
    }

    // Have to duplicate
    if ($this->_duplicate) {
      $new_obj = new CTaskingTicket();
      $new_obj->cloneFrom($this);

      // Don't duplicate the new object
      $new_obj->_duplicate = null;

      if ($msg = $new_obj->store()) {
        return $msg;
      }

      // Duplicate referenced tags
      if ($this->loadRefsTags()) {
        foreach ($this->_ref_tags as $_tag) {
          $tag_item               = new CTagItem();
          $tag_item->tag_id       = $_tag->_id;
          $tag_item->object_id    = $new_obj->_id;
          $tag_item->object_class = "CTaskingTicket";

          if ($msg = $tag_item->store()) {
            return $msg;
          }
        }
      }

      // Duplicate referenced messages
      if ($this->loadRefsMessages()) {
        foreach ($this->_ref_tasking_messages as $_message) {
          $_message->_id     = null;
          $_message->task_id = $new_obj->_id;

          if ($msg = $_message->store()) {
            return $msg;
          }
        }
      }
    }
  }

  /**
   * Check if the imported tag is already stored, and its parent too
   *
   * @param string $tag_name  Tag name
   * @param int    $ticket_id Ticket ID for CTagItem storing
   *
   * @return int
   */
  static function checkImportTag($tag_name, $ticket_id = null) {
    $tag = new CTag();
    $tag->name = utf8_decode($tag_name);
    $tag->object_class = "CTaskingTicket";
    $tag->loadMatchingObject();

    if (!$tag->_id) {
      $tag_rtm = new CTag();
      $tag_rtm->name = utf8_decode($tag_name);
      $tag_rtm->object_class = "CRTM";
      $tag_rtm->loadMatchingObject();

      $tag->color = $tag_rtm->color;

      // We have to load and store the parent tag
      if ($tag_rtm->parent_id) {
        $parent_tag = new CTag();
        $parent_tag->load($tag_rtm->parent_id);

        // Recursive check
        $tag->parent_id = self::checkImportTag($parent_tag->name);
      }

      if ($msg = $tag->store()) {
        CAppUI::setMsg($msg, UI_MSG_WARNING);
      }
    }

    // We are in "parent loop", have to return new ID
    if (!$ticket_id) {
      return $tag->_id;
    }

    // We have to create the link between the CTag and the CTaskingTicket => CTagItem
    $tag_item = new CTagItem();

    $tag_item->tag_id       = $tag->_id;
    $tag_item->object_id    = $ticket_id;
    $tag_item->object_class = "CTaskingTicket";

    if ($msg = $tag_item->store()) {
      CAppUI::setMsg($msg, UI_MSG_WARNING);
    }
  }
}
