/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

var UserMessage = {

  edit: function(usermessage_id, destinataire_id, oncloseCallback) {
    var url = new Url("messagerie", "ajax_edit_usermessage");
    url.addParam("usermessage_id", usermessage_id);
    url.addParam("usermessage_dest_id", destinataire_id);
    url.modal({width: 900, height: 420});
    url.modalObject.observe('afterClose', function() {
      if (oncloseCallback) {
        oncloseCallback();
      }
      else {
        window.location.reload();
      }
    });
  },
  
  create: function(to_id, in_reply_to) {
    var url = new Url("messagerie", "ajax_edit_usermessage");
    url.addParam("usermessage_id", 0);
    if (to_id) {
      url.addParam("to_id", to_id);
    }
    if (in_reply_to) {
      url.addParam("in_reply_to", in_reply_to);
    }
    url.modal({width: 900, height: 420});
  },

  refreshList : function(mode, start) {
    var oform = getForm('list_usermessage');
    if (oform) {
      $V(oform.mode, mode);
      $V(oform.page, start);
      oform.onsubmit();
    }
  },

  refreshListPage : function(page) {
    var oform = getForm("list_usermessage");
    if (oform) {
      $V(oform.page, page? page : 0);
      oform.onsubmit();
    }
  },

  refreshListCallback : function() {
    var oform = getForm('list_usermessage');
    if (oform) {
      oform.onsubmit();
    }
  },

  editAction : function(user_dest_id, action, value) {
    var url = new Url("messagerie", "ajax_do_action_usermessage");
    url.addParam("user_dest_id", user_dest_id);
    url.addParam("action", action);
    url.addParam("value", value);
    url.requestUpdate("systemMsg", {onComplete:
    UserMessage.refreshListCallback});
  }
};