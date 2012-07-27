/**
 * JS function Sender FS
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

SenderFS = {
  actor_guid: null,
  
  dispatch: function(actor_guid) {
    var url = new Url("system", "ajax_dispatch_files");
    url.addParam("actor_guid", actor_guid);
    url.requestUpdate("CSenderFileSystem-utilities_dispatch");
  },
  
  createExchanges: function(actor_guid) {
    var url = new Url("system", "ajax_dispatch_files");
    url.addParam("actor_guid"  , actor_guid);
    url.addParam("to_treatment", 0);
    url.requestUpdate("CSenderFileSystem-create_exchanges");
  }
};