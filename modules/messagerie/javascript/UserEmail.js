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
  url:   null,
  tab:  null,
  page: 0,

  modalPOPOpen:function (id, type) {
    var url = new Url(messagerie.module, "ajax_open_pop_email");
    url.addParam("mail_id", id);
    url.requestModal(800, 600);
    url.modalObject.observe("afterClose", this.refreshList.curry(type));
  },

  modalExternalOpen:function (id, account) {
    var url = new Url(messagerie.module, "ajax_open_external_email");
    url.addParam("mail_id", id);
    url.requestModal(900, 800);
    url.modalObject.observe("afterClose", this.refreshList.curry(messagerie.page, account));
    messagerie.url = url;
  },

  refreshList:function (page,account) {
    if( page != messagerie.page) {
      messagerie.page = page;
    }
    if (account) {
      messagerie.tab = account;
    }
    var url = new Url(messagerie.module, "ajax_list_mails");
    url.addParam("account", messagerie.tab);
    url.addParam("page", messagerie.page);
    url.requestUpdate(messagerie.tab);
  },

  getLastMessages:function (user_id) {
    var url = new Url(messagerie.module, "ajax_get_last_email");
    url.addParam("user_id", user_id);
    url.requestUpdate("systemMsg", function () {
      messagerie.refreshList(0, messagerie.tab);
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


  markallAsRead: function () {
    var url = new Url("messagerie", "ajax_mark_all_mail_as_read");
    url.addParam("account", messagerie.tab);
    url.requestUpdate("systemMsg", function () {
      messagerie.refreshList(0, messagerie.tab);
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
    //var selected = table.select("input[name='attach_item']:checked");

  }
};