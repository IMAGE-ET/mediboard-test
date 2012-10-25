/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

var MbMail = {
  edit: function(usermessage_id) {
    var url = new Url("messagerie", "write_usermessage");
    url.addParam("usermessage_id", usermessage_id);
    url.modal(800, 500);
  },
  
  create: function(to_id, subject) {
    var url = new Url("messagerie", "write_usermessage");
    url.addParam("usermessage_id", 0);
    if(to_id) {
      url.addParam("to", to_id);
    }
    if (subject) {
      url.addParam("subject", subject);
    }
    url.modal(800, 800);
  }
};