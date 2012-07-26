/**
 * JS function Sender FTP EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

SenderFTP = {
  actor_guid: null,
  
  dispatch: function(actor_guid) {
  var url = new Url("ftp", "ajax_dispatch_files_ftp");
    url.addParam("actor_guid", actor_guid);
    url.requestUpdate("CSenderFTP-utilities_dispatch");
  },
  
  readFilesSenders: function() {
    var url = new Url("ftp", "ajax_read_ftp_files");
    url.requestUpdate("CSenderFTP-utilities_read-files-senders");
  }
};