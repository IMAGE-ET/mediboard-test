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

Tasking = {
  editTaskingTicket : function(tasking_ticket_id) {
    var url = new Url('tasking', 'ajax_edit_tasking_ticket');
    var tasking_ticket_id = tasking_ticket_id;
    url.addParam('tasking_ticket_id', tasking_ticket_id);

    url.requestModal("90%", "70%", {onClose: function() {
      Tasking.redoSearch();
    }});
  },

  listTaskingMessages : function(tasking_ticket_id) {
    var url = new Url('tasking', 'ajax_list_tasking_ticket_messages');
    url.addParam('tasking_ticket_id', tasking_ticket_id);

    url.requestUpdate("tasking-messages");
  },

  smartAddTaskingTicket : function (form) {
    var url = new Url('tasking', 'ajax_edit_tasking_ticket');

    var task_smart = $V(form.elements.task_smart);
    url.addParam('task_smart', task_smart);

    url.requestModal("90%", "70%");

    $V(form.elements.task_smart, '');

    return false;
  },

  submitTaskingTicket : function(form) {
    var date = new Date();
    $V(form.elements.last_modification_date, date.toDATETIME());

    return onSubmitFormAjax(
      form,
      {onComplete: function() {
          Control.Modal.close();
          Tasking.redoSearch();
        }
      }
    );
  },

  redoSearch : function() {
    var form = getForm("search-tickets");
    form.onsubmit();
  },

  showHelp : function() {
    var url = new Url('tasking', 'ajax_show_help');
    url.requestModal(300, 200);
  },

  searchTaskingTickets : function(form) {
    var url = new Url("tasking", "ajax_show_tasking_tickets");
    url.addFormData(form);
    url.requestUpdate("tickets-results");

    return false;
  },

  bindTag : function(form, id, action, color, name) {
    switch (action) {
      case "addTag":
        var aTagsToRemove = $V(form.elements.tags_to_remove);
        if (aTagsToRemove != "") {
          aTagsToRemove = aTagsToRemove.split("|");
          if (aTagsToRemove.indexOf(id) > -1) {
            aTagsToRemove.splice(aTagsToRemove.indexOf(id), 1);
            $V(form.elements.tags_to_remove, aTagsToRemove.join("|"));

            $('tags').insert(
              "<li data-tag_item_id='"+id+"' id='CTag-"+id+"' style='background-color: "+color+"' class='tag'>"+name+
                "<button type='button' class='delete' " +
                "onclick='Tasking.bindTag(form, \$(this).up(\"li\").get(\"tag_item_id\"), \"removeTag\");'>" +
                "</li>");
            break;
          }
        }

        var aTagsToAdd = $V(form.elements.tags_to_add);
        if (aTagsToAdd != "") {
          aTagsToAdd = aTagsToAdd.split("|");
        }
        else {
          aTagsToAdd = [];
        }

        aTagsToAdd.push(id);
        $V(form.elements.tags_to_add, aTagsToAdd.join("|"));
        $('tags').insert(
          "<li data-tag_item_id='"+id+"' id='CTag-"+id+"' style='background-color: "+color+"' class='tag'>"+name+
            "<button type='button' class='delete' " +
            "onclick='Tasking.bindTag(form, \$(this).up(\"li\").get(\"tag_item_id\"), \"removeTag\");'>" +
            "</li>");
        break;

      case "removeTag":
        var aTagsToAdd = $V(form.elements.tags_to_add);
        if (aTagsToAdd != "") {
          aTagsToAdd = aTagsToAdd.split("|");
          if (aTagsToAdd.indexOf(id) > -1) {
            aTagsToAdd.splice(aTagsToAdd.indexOf(id), 1);
            $V(form.elements.tags_to_add, aTagsToAdd.join("|"));

            $('CTag-'+id).remove();
            break;
          }
        }

        var aTagsToRemove = $V(form.elements.tags_to_remove);
        if (aTagsToRemove != "") {
          aTagsToRemove = aTagsToRemove.split("|");
        }
        else {
          aTagsToRemove = [];
        }

        aTagsToRemove.push(id);
        $V(form.elements.tags_to_remove, aTagsToRemove.join("|"));
        $('CTag-'+id).remove();
        break;

      case "addSearchTag":
        var aTagsToSearch = $V(form.elements.tags_to_search);
        if (aTagsToSearch != "") {
          aTagsToSearch = aTagsToSearch.split("|");
        }
        else {
          aTagsToSearch = [];
        }

        aTagsToSearch.push(id);
        $V(form.elements.tags_to_search, aTagsToSearch.join("|"));
        $('search-tags').insert(
          "<li data-tag_item_id='"+id+"' id='CTag-"+id+"' style='background-color: "+color+"' class='tag'>"+name+
            "<button type='button' class='delete' " +
            "onclick='Tasking.bindTag(form, \$(this).up(\"li\").get(\"tag_item_id\"), \"removeSearchTag\");'>" +
            "</li>");
        break;

      case "removeSearchTag":
        var aTagsToSearch = $V(form.elements.tags_to_search);
        aTagsToSearch = aTagsToSearch.split("|");

        aTagsToSearch.splice(aTagsToSearch.indexOf(id), 1);
        $V(form.elements.tags_to_search, aTagsToSearch.join("|"));
        $('CTag-'+id).remove();
    }
  },

  postpone : function(form) {
    var due_date = $V(form.elements.due_date);
    var today    = new Date();

    if (due_date == "") {
      $V(form.elements.due_date, today.toDATETIME());
    }
    else {
      due_date = Date.fromDATETIME(due_date);

      if (due_date < today) {
        $V(form.elements.due_date, today.toDATETIME());
      }
      else {
        due_date.addDays(1);
        $V(form.elements.due_date, due_date.toDATETIME());
      }
    }

    $V(form.elements.nb_postponements, parseInt($V(form.elements.nb_postponements)) + 1);
    form.onsubmit();
  },

  duplicate : function(form) {
    $V(form.elements._duplicate, 1);
    $V(form.elements.callback, "Tasking.afterDuplicate");
    form.onsubmit();
  },

  afterDuplicate : function(tasking_ticket_id) {
    this.editTaskingTicket(tasking_ticket_id);
  },

  closeTaskingTicket : function(form) {
    var date = new Date();
    $V(form.elements.closing_date, date.toDATETIME());
    $V(form.elements.status, "closed");
    form.onsubmit();
  },

  confirmDeletion : function(form, options) {
    confirmDeletion(form, options);
  },

  checkStatus : function(checkbox) {
    var aStatus    = $V(checkbox).split("|");
    var lastStatus = aStatus[aStatus.length-1];

    var form = getForm("search-tickets");

    var index;
    switch (lastStatus) {
      case "new":
      case "accepted":
      case "inprogress":
        index = aStatus.indexOf("invalid");
        if (index != -1) {
          aStatus.splice(index, 1);
          form.elements.__status_invalid.checked = false;
        }

        index = aStatus.indexOf("duplicate");
        if (index != -1) {
          aStatus.splice(index, 1);
          form.elements.__status_duplicate.checked = false;
        }

        index = aStatus.indexOf("cancelled");
        if (index != -1) {
          aStatus.splice(index, 1);
          form.elements.__status_cancelled.checked = false;
        }

        index = aStatus.indexOf("closed");
        if (index != -1) {
          aStatus.splice(index, 1);
          form.elements.__status_closed.checked = false;
        }

        index = aStatus.indexOf("refused");
        if (index != -1) {
          aStatus.splice(index, 1);
          form.elements.__status_refused.checked = false;
        }

        break;

      case "invalid":
      case "duplicate":
      case "cancelled":
      case "closed":
      case "refused":
        index = aStatus.indexOf("new");
        if (index != -1) {
          aStatus.splice(index, 1);
          form.elements.__status_new.checked = false;
        }

        index = aStatus.indexOf("accepted");
        if (index != -1) {
          aStatus.splice(index, 1);
          form.elements.__status_accepted.checked = false;
        }

        index = aStatus.indexOf("inprogress");
        if (index != -1) {
          aStatus.splice(index, 1);
          form.elements.__status_inprogress.checked = false;
        }
    }

    $V(form.elements._status, aStatus.join("|"));
  },

  selectAllTasks : function(state) {
    $$(".checkTask").each(function(elt) {
      elt.checked = state;
    });
  },

  multipleTaskingTickets : function(action) {
    if (!action) {
      return;
    }

    if (action == "delete") {
      Modal.confirm("Voulez-vous supprimer ces tâches ?", {onValidate: function(v) { if (v) { Tasking.doAction(action); } } });
    }
    else {
      this.doAction(action);
    }
  },

  doAction : function(action) {
    var aTasks = [];

    $$(".checkTask").each(function(elt) {
      if (elt.checked) {
        aTasks.push(elt.get('id'));
      }
    });

    if (aTasks.length > 0) {
      var url = new Url('tasking', 'controllers/do_multiple_aed');
      url.addParam("action", action);
      url.addParam("tasks", Object.toJSON(aTasks));
      url.requestUpdate("systemMsg", {onComplete: function() {
        Tasking.redoSearch();
      }});
    }
  }
}