/**
 * JS function Interop Actor EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

InteropActor = {
  actor_guid : null,
  
  refreshActor : function(actor_guid, actor_class_name){
    var url = new Url("eai", "ajax_refresh_actor");
    url.addParam("actor_guid", actor_guid);
    url.addParam("actor_class_name", actor_class_name);
    url.requestUpdate("actor");
  },
  
  refreshActors : function(parent_class_name) {
	var url = new Url("eai", "ajax_refresh_actors");
	url.addParam("actor_class_name", parent_class_name);
	url.requestUpdate(parent_class_name+"s");
  },
  
  refreshActorsAndActor : function(actor_id){
	InteropActor.refreshActor(InteropActor.actor_guid.split('-')[0]+"-"+actor_id);  
	InteropActor.refreshActors($V(getForm("edit"+InteropActor.actor_guid).parent_class_name));
  },
  
  receive : function(actor_guid) {
    var url = new Url("eai", "ajax_receive_files");
	url.addParam("actor_guid", actor_guid);
	url.requestUpdate("utilities-exchange-source-receive");
  },
  
  refreshFormatsAvailable : function(actor_guid) {
    var url = new Url("eai", "ajax_refresh_formats_available");
    url.addParam("actor_guid", actor_guid);
    url.requestUpdate("formats_available_"+actor_guid);
  },
  
  viewMessagesSupported : function(actor_guid, exchange_class_name) {
	var url = new Url("eai", "ajax_vw_messages_supported");
	url.addParam("actor_guid", actor_guid);
	url.addParam("exchange_class_name", exchange_class_name);
    url.requestModal(800, 350);
  },
};