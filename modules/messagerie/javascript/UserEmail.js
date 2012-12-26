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
  module : "messagerie",

  modalPOPOpen: function(id, type) {
    var url = new Url(messagerie.module, "ajax_open_pop_email");
    url.addParam("mail_id"    , id);
    url.requestModal(800,600);
    url.modalObject.observe("afterClose", this.refreshList.curry(type));
  },

  modalExternalOpen: function(id, type) {
    var url = new Url(messagerie.module, "ajax_open_external_email");
    url.addParam("mail_id"    , id);
    url.requestModal(800,600);
    url.modalObject.observe("afterClose", this.refreshList.curry(type));
  },

  refreshList : function(type) {
    var url = new Url(messagerie.module, "ajax_list_mails");
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
   }

}