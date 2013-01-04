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
console.log("toto");
messagerie = {
  module : "messagerie",
  url : null,

  modalPOPOpen: function(id, type) {
    var url = new Url(messagerie.module, "ajax_open_pop_email");
    url.addParam("mail_id"    , id);
    url.requestModal(800,600);
    url.modalObject.observe("afterClose", this.refreshList.curry(type));
  },

  modalExternalOpen: function(id, type) {
    var url = new Url(messagerie.module, "ajax_open_external_email");
    url.addParam("mail_id"    , id);
    url.requestModal(900,800);
    url.modalObject.observe("afterClose", this.refreshList.curry(type));
    messagerie.url = url;
  },

  refreshList : function(type) {
    var url = new Url(messagerie.module, "ajax_list_mails");

    if (Object.isUndefined(type)) {
      type = 'all';
    }
    url.addParam("type", type);
    url.requestUpdate(type);
  },

  getLastMessages : function(user_id,type) {
    if(!type) { type = 'all';}
    var url = new Url(messagerie.module, "ajax_get_last_email");
    url.addParam("user_id"    , user_id);
    url.requestUpdate("systemMsg", function() {
      messagerie.refreshList(type);
    });
   },

  /*
   * Get the attachment from POP serveur but not save it in mediboard, only for preview
   */
  AttachFromPOP : function(mail_id, part) {
    var url = new Url("messagerie", "get_pop_attachment");
    url.addParam("mail_id", mail_id);
    url.addParam("part", part);
    url.requestModal(800,800);
  },

  getAttachment : function(attachment_id) { //récupère les attachments et les lie aux CMailAttachment (création des CFile)
    var url = new Url("messagerie", "pop_attachment_to_cfile");
    url.addParam("attachment_id",attachment_id);
    url.requestUpdate("systemMsg", function() {
      messagerie.url.refreshModal();
    });
},
  /**
   * Toggle a list of checkbox
   *
   * @param table_id
   * @param status
   * @param item_class
   */
  toggleSelect: function(table_id, status, item_class) {
    var table = $(table_id);
      table.select("input[name="+item_class+"]").each(function(elt) {
        elt.checked = status ? "checked" : "";
      });
  },

  /**
   * Link a list of attachment to a folder.
   */
  linkSelectedAttachment : function() {

  }

}