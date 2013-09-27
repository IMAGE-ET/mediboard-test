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

if (!Tasking) Tasking = {};

Tasking.Message = {
  editMessage : function(task_id, message_id) {
    var url = new Url('tasking', 'ajax_edit_tasking_ticket_message');
    url.addParam('task_id',     task_id);
    url.addParam('message_id', message_id);

    url.requestModal("40%");
  },

  submitMessage : function(form) {
    var formEdit = getForm("edit-tasking_ticket");
    return onSubmitFormAjax(
      form,
      {onComplete: function() {
        Control.Modal.close();
        Tasking.listTaskingMessages($V(formEdit.elements.tasking_ticket_id));
        }
      }
    );
  },

  closeAndList : function() {
    var form     = getForm("edit-message");
    return Tasking.Message.submitMessage(form);
  }
}
