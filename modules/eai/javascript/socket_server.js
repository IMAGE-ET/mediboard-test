/**
 * JS function MLLP Server
 *  
 * @category EAI
 * @package  Mediboard
 * @subpackage eai
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

SocketServer = {
  action : function(port, type, process_id, uid, action){
    var url = new Url("eai", "ajax_socket_server_action");
    url.addParam("port", port);
    url.addParam("type", type);
    url.addParam("uid", uid);
    url.addParam("process_id", process_id);
    url.addParam("action", action);
    if (action == "stats" || action == "test") {
      url.requestUpdate("stats_"+uid);
      return;
    }
    url.requestUpdate(uid);
  },
  
  trash : function(process_id, uid){
    var url = new Url("eai", "ajax_socket_server_trash");
    url.addParam("uid", uid);
    url.addParam("process_id", process_id);
    url.requestUpdate(uid);
  }
}