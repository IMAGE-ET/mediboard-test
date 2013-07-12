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
    url.requestUpdate(messagerie.mode);

  },

  refreshListPage : function(page) {
    this.refreshList(null, null, page);
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
   * refresh the list of attachments to link
   *
   * @param mail_id
   */
  listAttachLink : function(mail_id) {
    var url = new Url(messagerie.module, "ajax_list_attachments");
    url.addParam("mail_id", mail_id);
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
  }
};