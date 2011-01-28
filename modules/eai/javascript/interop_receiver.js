/**
 * JS function Interop Receiver EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

InteropReceiver = {
  receiver_guid : null,
  status_images : ["images/icons/status_red.png", "images/icons/status_orange.png", "images/icons/status_green.png"],
		
  refreshReceiver : function(receiver_guid, receiver_class_name){
    var url = new Url("eai", "ajax_refresh_receiver");
    url.addParam("receiver_guid", receiver_guid);
    url.addParam("receiver_class_name", receiver_class_name);
    url.requestUpdate("receiver");
  },

  refreshReceivers : function(){
    var url = new Url("eai", "ajax_refresh_receivers");
    url.requestUpdate("receivers");
  },
  
  refreshReceiverExchangesSources : function(receiver_guid){
    var url = new Url("eai", "ajax_refresh_receiver_exchanges_sources");
    url.addParam("receiver_guid", receiver_guid);
    url.requestUpdate("receiver_exchanges_sources");
  },
  
  refreshReceiversAndReceiver : function(receiver_id){
	InteropReceiver.refreshReceivers();   
	InteropReceiver.refreshReceiver(InteropReceiver.receiver_guid.split('-')[0]+"-"+receiver_id);  
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
      element.src = InteropReceiver.status_images[status.reachable];
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
  }
};