/**
 * JS function MLLP Server
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

MLLPServer = {
  action : function(port, process_id, uid, action){
    var url = new Url("hl7", "ajax_mllp_server_action");
    url.addParam("port", port);
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
    var url = new Url("hl7", "ajax_mllp_server_trash");
    url.addParam("uid", uid);
    url.addParam("process_id", process_id);
    url.requestUpdate(uid);
  }
}