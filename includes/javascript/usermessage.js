/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

var UserMessage = {
  edit: function(usermessage_id) {
    var url = new Url("messagerie", "write_usermessage");
    url.addParam("usermessage_id", usermessage_id);
    url.modal(800, 500);
    url.modalObject.observe('afterClose', UserMessage.refresh);
  },
  
  create: function(to_id, subject, in_reply_to) {
    var url = new Url("messagerie", "write_usermessage");
    url.addParam("usermessage_id", 0);
    if(to_id) {
      url.addParam("to", to_id);
    }
    if (subject) {
      url.addParam("subject", subject);
    }
    if (in_reply_to) {
      url.addParam("in_reply_to", in_reply_to);
    }
    url.modal(800, 800);
    url.modalObject.observe('afterClose', UserMessage.refresh);
  },

  refresh: function() {}
};