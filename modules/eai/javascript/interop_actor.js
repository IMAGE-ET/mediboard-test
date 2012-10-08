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
  modal:       null,
  
  refreshActor : function(actor_guid, actor_class){
    var url = new Url("eai", "ajax_refresh_actor");
    url.addParam("actor_guid", actor_guid);
    url.addParam("actor_class", actor_class);
    url.requestUpdate("actor");
  },
  
  refreshActors : function(parent_class) {
    var url = new Url("eai", "ajax_refresh_actors");
    url.addParam("actor_class", parent_class);
    url.requestUpdate(parent_class+"s");
  },
  
  refreshActorsAndActor : function(actor_id){
    InteropActor.refreshActor(InteropActor.actor_guid.split('-')[0]+"-"+actor_id);  
    InteropActor.refreshActors($V(getForm("edit"+InteropActor.actor_guid).parent_class));
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
  
  refreshExchangesSources : function(actor_guid, message) {
    var url = new Url("eai", "ajax_refresh_exchanges_sources");
    url.addParam("actor_guid", actor_guid);
    url.addParam("message", message);
    url.requestUpdate("exchanges_sources_"+actor_guid);  
  },
  
  viewMessagesSupported : function(actor_guid, exchange_class) {
    var url = new Url("eai", "ajax_vw_messages_supported");
    url.addParam("actor_guid", actor_guid);
    url.addParam("exchange_class", exchange_class);
    url.requestModal(800, 350);
    InteropActor.modal = url.modalObject;
    InteropActor.modal.observe("afterClose", function(){ 
      InteropActor.refreshFormatsAvailable(actor_guid); 
    });
  },
  
  callbackConfigsFormats : function(config_id, object) {
    var actor_guid = object.sender_class+"-"+object.sender_id;
    InteropActor.refreshConfigsFormats(actor_guid);
  },
  
  refreshConfigsFormats : function(actor_guid) {
    var url = new Url("eai", "ajax_refresh_configs_formats");
    url.addParam("actor_guid", actor_guid);
    url.requestUpdate("configs_formats_"+actor_guid);
  },
  
  viewConfigsFormat : function(actor_guid, config_guid) {
    var url = new Url("eai", "ajax_vw_configs_format");
    url.addParam("actor_guid", actor_guid);
    url.addParam("config_guid", config_guid);
    url.requestUpdate("format_"+config_guid);
  },
  
  refreshConfigObjectValues : function(object_id, object_configs_guid) {
    var url = new Url("system", "ajax_config_object_values");
    url.addParam("object_id", object_id);
    url.addParam("object_configs_guid", object_configs_guid);
    url.requestUpdate("actor_config_"+object_id);
  },
  
  refreshTags : function(actor_guid) {
    var url = new Url("eai", "ajax_refresh_tags");
    url.addParam("actor_guid", actor_guid);
    url.requestUpdate("tags_"+actor_guid);
  }
};