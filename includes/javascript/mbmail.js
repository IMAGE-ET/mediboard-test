/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

var MbMail = {
  edit: function(mbmail_id) {
    var url = new Url("messagerie", "write_mbmail");
    url.addParam("mbmail_id", mbmail_id);
    url.popup(650, 500, "MbMail");
  },
  
  create: function(to_id, subject) {
    var url = new Url("messagerie", "write_mbmail");
    url.addParam("mbmail_id", 0);
    if(to_id) {
      url.addParam("to", to_id);
    }
    if (subject) {
      url.addParam("subject", subject);
    }
    url.popup(650, 500, "MbMail");
  }
};