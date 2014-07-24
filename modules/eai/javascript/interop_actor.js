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

  editActor: function(actor_guid, actor_class, parent_class) {
    var url = new Url("eai", "ajax_edit_actor");
    url.addParam("actor_guid", actor_guid);
    url.addParam("actor_class", actor_class)
    url.requestModal("500");
    InteropActor.modal = url.modalObject;
    InteropActor.modal.observe("afterClose", function(){
      if (!actor_guid) {
        InteropActor.refreshActors(parent_class);
      }
      else {
        InteropActor.refreshActor(actor_guid, actor_class);
      }
    });
  },

  refreshActor : function(actor_guid, actor_class){
    new Url("eai", "ajax_refresh_actor")
      .addParam("actor_guid", actor_guid)
      .addParam("actor_class", actor_class)
      .requestUpdate("line_"+actor_guid);
  },

  viewActor : function(actor_guid, actor_class){
    new Url("eai", "ajax_view_actor")
      .addParam("actor_guid", actor_guid)
      .addParam("actor_class", actor_class)
      .requestUpdate("actor");
  },
  
  refreshActors : function(parent_class) {
    new Url("eai", "ajax_refresh_actors")
      .addParam("actor_class", parent_class)
      .requestUpdate(parent_class+"s");
  },
  
  refreshActorsAndActor : function(actor_id) {
    InteropActor.refreshActor(InteropActor.actor_guid.split('-')[0]+"-"+actor_id);  
    InteropActor.refreshActors($V(getForm("edit"+InteropActor.actor_guid).parent_class));
  },
  
  receive : function(actor_guid) {
    new Url("eai", "ajax_receive_files")
      .addParam("actor_guid", actor_guid)
      .requestUpdate("utilities-exchange-source-receive");
  },
  
  refreshFormatsAvailable : function(actor_guid) {
    new Url("eai", "ajax_refresh_formats_available")
      .addParam("actor_guid", actor_guid)
      .requestUpdate("formats_available_"+actor_guid);
  },
  
  refreshExchangesSources : function(actor_guid, message) {
    new Url("eai", "ajax_refresh_exchanges_sources")
      .addParam("actor_guid", actor_guid)
      .addParam("message", message)
      .requestUpdate("exchanges_sources_"+actor_guid);
  },
  
  viewMessagesSupported : function(actor_guid, exchange_class) {
    var url = new Url("eai", "ajax_vw_messages_supported");
    url.addParam("actor_guid", actor_guid);
    url.addParam("exchange_class", exchange_class);
    url.requestModal("90%", "85%");
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
    new Url("eai", "ajax_refresh_configs_formats")
      .addParam("actor_guid", actor_guid)
      .requestUpdate("configs_formats_"+actor_guid);
  },
  
  viewConfigsFormat : function(actor_guid, config_guid) {
    new Url("eai", "ajax_vw_configs_format")
      .addParam("actor_guid", actor_guid)
      .addParam("config_guid", config_guid)
      .requestUpdate("format_"+config_guid);
  },
  
  refreshConfigObjectValues : function(object_id, object_configs_guid) {
    new Url("system", "ajax_config_object_values")
      .addParam("object_id", object_id)
      .addParam("object_configs_guid", object_configs_guid)
      .requestUpdate("actor_config_"+object_id);
  },
  
  refreshTags : function(actor_guid) {
    new Url("eai", "ajax_refresh_tags")
      .addParam("actor_guid", actor_guid)
      .requestUpdate("tags_"+actor_guid);
  },

  refreshLinkedObjects : function(actor_guid) {
    new Url("eai", "ajax_refresh_linked_objects")
      .addParam("actor_guid", actor_guid)
      .requestUpdate("linked_objects_" + actor_guid);
  },

  refreshRoutes : function(actor_guid) {
    new Url("eai", "ajax_refresh_sender_routes")
      .addParam("actor_guid", actor_guid)
      .requestUpdate("routes_" + actor_guid);
  }
};