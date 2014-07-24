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
    new Url("system", "ajax_dispatch_files")
      .addParam("actor_guid", actor_guid)
      .requestModal("60%");
  },
  
  createExchanges: function(actor_guid) {
    new Url("system", "ajax_dispatch_files")
      .addParam("actor_guid"  , actor_guid)
      .addParam("to_treatment", 0)
      .requestModal("60%");
  }
};