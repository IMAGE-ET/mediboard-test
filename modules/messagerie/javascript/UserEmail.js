/**
 * JS function UserEmail
 *
 * @category messagerie
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */
messagerie = {
  module:"messagerie",
  mode : "unread",
  account_id : '0',
  url:   null,
  tab:  null,
  page: 0,

  modalExternalOpen:function (id, account) {
    var url = new Url(messagerie.module, "ajax_open_external_email");
    url.addParam("mail_id", id);
    url.requestModal(-20, -20, {onClose: messagerie.refreshList.curry(null, null, null)});
  },

  //refresh div
  refreshAccount:function(account_id) {
    var url = new Url(messagerie.module, "vw_user_external_mail");
    url.addParam("account_id", account_id);
    url.requestUpdate("account_mail");
  },

  refreshList:function (account_id, mode, page) {
    var url = new Url(messagerie.module, "ajax_list_mails");

    messagerie.account_id = (account_id != null) ? account_id : messagerie.account_id ;
    messagerie.mode       = (mode != null)? mode : messagerie.mode;
    messagerie.page       = (page != null)? page : messagerie.page;

    url.addParam("account_id", messagerie.account_id);
    url.addParam("mode", messagerie.mode);
    url.addParam("page", messagerie.page);
    url.requestUpdate('list-messages', {onComplete: messagerie.refreshCounts});

  },

  refreshListPage : function(page) {
    this.refreshList(null, null, page);
  },

  /**
   * Do an action for one or several mails
   *
   * @param string action  The action to perform
   * @param int    mail_id (Optional) The id of a mail
   */
  action : function(action, mail_id) {
    var url = new Url('messagerie', 'ajax_do_action_usermail');
    url.addParam('action', action);

    if (mail_id) {
      url.addParam('usermail_ids', JSON.stringify([mail_id]));
    }
    else {
      url.addParam('usermail_ids', this.getSelectedMails());
    }

    url.requestUpdate('systemMsg', {onComplete: messagerie.refreshList})
  },

  /**
   * Create a new mail, or edit one
   *
   * @param int  mail_id       (Optional) The id of the mail to edit, if not given, a ne mail will be created
   * @param int  reply_to_id   (Optional) The id of the mail to answer
   * @param bool answer_to_all (Optional) True for answering to all the recipients
   */
  edit: function(mail_id, reply_to_id, answer_to_all) {
    var url = new Url('messagerie', 'ajax_edit_usermail');
    url.addParam('account_id', messagerie.account_id);
    if (mail_id) {
      url.addParam('mail_id', mail_id);
    }
    if (reply_to_id) {
      url.addParam('reply_to_id', reply_to_id);
    }
    if (answer_to_all) {
      url.addParam('answer_to_all', answer_to_all);
    }

    url.modal({width: -40, height: -40, onClose: messagerie.refreshList()});
  },

  /**
   * Mark a folder as selected, and reload the list of mails
   *
   * @param int    account_id The account id
   * @param string folder     The name of the folder
   */
  selectFolder: function(account_id, folder) {
    var old_icon = $$('div.folder.selected i.folder-icon')[0];
    old_icon.removeClassName('fa-folder-open');
    old_icon.addClassName('fa-folder');
    $$('div.folder.selected')[0].removeClassName('selected');
    $$('div.folder[data-folder=' + folder + ']')[0].addClassName('selected');
    var new_icon = $$('div.folder.selected i.folder-icon')[0];
    new_icon.removeClassName('fa-folder');
    new_icon.addClassName('fa-folder-open');

    messagerie.refreshList(account_id, folder);
  },

  toggleFavorite : function(mail_id) {
    var url = new Url(messagerie.module, "controllers/do_toggle_favorite");
    url.addParam("mail_id", mail_id);
    url.requestUpdate("systemMsg", function() {
      messagerie.refreshList();
    });
  },

  toggleArchived : function(mail_id) {
    var url = new Url(messagerie.module, "controllers/do_toggle_archived");
    url.addParam("mail_id", mail_id);
    url.requestUpdate("systemMsg", function() {
      messagerie.refreshList();
    });
  },

  /**
   * Return the ids of the selected mails
   *
   * @returns string
   */
  getSelectedMails: function() {
    var selected_mails = $$('tr.message input[type=checkbox]:checked');
    var mails_id = [];

    selected_mails.each(function(message) {
      mails_id.push(message.getAttribute('value'));
    });

    return JSON.stringify(mails_id);
  },

  openMailDebug: function(mail_id) {
    var url = new Url(messagerie.module, 'vw_pop_mail');
    url.addParam('id', mail_id);
    url.requestModal();
  },

  /**
   * refresh the list of attachments to link
   *
   * @param mail_id
   */
  listAttachLink : function(mail_id, rename) {
    var url = new Url(messagerie.module, "ajax_list_attachments");
    url.addParam("mail_id", mail_id);
    url.addParam("rename", rename);
    url.requestUpdate("list_attachments");
  },

  getLastMessages:function (account_id) {
    var url = new Url(messagerie.module, "cron_update_pop");
    url.addParam("account_id", account_id);
    url.requestUpdate("systemMsg", function () {
      messagerie.refreshAccount(account_id);
    });
  },

  /*
   * Get the attachment from POP serveur but not save it in mediboard, only for preview
   */
  AttachFromPOP:  function (mail_id, part) {
    var url = new Url("messagerie", "get_pop_attachment");
    url.addParam("mail_id", mail_id);
    url.addParam("part", part);
    url.requestModal(800, 800);
  },

  getAttachment:function (mail_id,all) { //récupère les attachments et les lie aux CMailAttachment (création des CFile)
    var url = new Url("messagerie", "pop_attachment_to_cfile");
    url.addParam("mail_id", mail_id);
    url.addParam("attachment_id", all);
    url.requestUpdate("systemMsg", function () {
      messagerie.url.refreshModal();
    });
  },


  markallAsRead: function (account_id) {
    var url = new Url("messagerie", "controllers/do_mark_all_mail_as_read");
    url.addParam("account_id", account_id);
    url.requestUpdate("systemMsg", function () {
      messagerie.refreshList();
    });
  },

  /**
   * Toggle a list of checkbox
   *
   * @param table_id
   * @param status
   * @param item_class
   */
  toggleSelect: function (table_id, status, item_class) {
    var table = $(table_id);
    table.select("input[name=" + item_class + "]").each(function (elt) {
      elt.checked = status ? "checked" : "";
    });
  },

  /**
   * Link a list of attachment to a folder.
   */
  linkAttachment:function (mail_id) {
    var url = new Url("messagerie", "ajax_link_attachments");
    url.addParam("mail_id", mail_id);
    url.requestModal(800,600);
  },

  dolinkAttachment: function (attach, mail_id) {
    var url = new Url("messagerie", "ajax_do_link_attachments");
    url.addParam("object_id", attach.id);
    url.addParam("object_class", attach.object);
    url.addParam("attach_list", attach.files);
    url.addParam("text_plain_id", attach.plain);
    url.addParam("text_html_id", attach.html);
    url.addParam("category_id", attach.category_id);
    url.addParam("rename_text", attach.rename_text);
    url.addParam("mail_id", mail_id);
    url.requestUpdate("systemMsg", function() {
      messagerie.listAttachLink(mail_id);
    });
  },

  cancelAttachment : function(attach_id, mail_id) {
    var url = new Url("messagerie", "ajax_do_unlink_attachment");
    url.addParam("attachment_id", attach_id);
    url.requestUpdate("systemMsg", function() {
      messagerie.listAttachLink(mail_id);
    });
  },

  /**
   * Display the view for adding attachments to a mail
   *
   * @param int mail_id The id of the mail
   */
  addAttachment: function(mail_id) {
    var url = new Url('messagerie', 'ajax_add_mail_attachment');
    url.addParam('mail_id', mail_id);
    url.requestModal(700, 300, {onClose: messagerie.reloadAttachments.curry(mail_id)});
  },

  /**
   * Reload the attachments for the given mail
   *
   * @param int mail_id The id of the mail
   */
  reloadAttachments: function(mail_id) {
    var url = new Url('messagerie', 'ajax_reload_mail_attachments');
    url.addParam('mail_id', mail_id);
    url.requestUpdate('list_attachments');
  },

  /**
   * Refresh the counts for all the folders
   */
  refreshCounts: function() {
    var url = new Url('messagerie', 'ajax_refresh_counts_usermails');
    url.addParam('account_id', messagerie.account_id);
    url.requestJSON(function(data) {
      data.each(function (folder) {
        var element = $$('div.folder[data-folder=' + folder.name + ']')[0].down('span.count');
        element.innerHTML = folder.count;
        if (folder.count > 0) {
          element.show();
        }
        else {
          element.hide();
        }
      });
    });
  }
};