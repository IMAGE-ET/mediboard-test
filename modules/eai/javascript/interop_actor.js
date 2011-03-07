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
  status_images : ["images/icons/status_red.png", "images/icons/status_orange.png", "images/icons/status_green.png"],
		
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
  
  resfreshImageStatus : function(element){
    if (!element.getAttribute('data-id')) {
      return;
    }

    var url = new Url("eai", "ajax_get_source_status");
    
    element.title = "";
    element.src   = "style/mediboard/images/icons/loading.gif";
    
    url.addParam("source_guid", element.getAttribute('data-guid'));
    url.requestJSON(function(status) {
      element.src = InteropActor.status_images[status.reachable];
      element.onmouseover = function() { 
        ObjectTooltip.createDOM(element, 
          DOM.div(null, 
            DOM.table({className:"main tbl", style:"max-width:350px"}, 
              DOM.tr(null,
                DOM.th(null, status.name)
              ), 
              DOM.tr(null,
                DOM.td({className:"text"}, 
                  DOM.strong(null, "Message : "), status.message)
             ), 
             DOM.tr(null,
	           DOM.td({className:"text"}, 
	             DOM.strong(null, "Temps de réponse : "), status.response_time, " ms")
	         )
           )
         ).hide()) 
      };
    });
  },
  
  receive : function(actor_guid) {
    var url = new Url("eai", "ajax_receive_files");
	url.addParam("actor_guid", actor_guid);
	url.requestUpdate("utilities-exchange-source-receive");
  }
};