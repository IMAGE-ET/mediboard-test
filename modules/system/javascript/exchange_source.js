/**
 * JS function Exchange Source
 *  
 * @category system
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

ExchangeSource = {
  status_images : ["images/icons/status_red.png", "images/icons/status_orange.png", "images/icons/status_green.png"],
  
  resfreshImageStatus : function(element){
    if (!element.getAttribute('data-id')) {
      return;
    }

    var url = new Url("system", "ajax_get_source_status");
    
    element.title = "";
    element.src   = "style/mediboard/images/icons/loading.gif";
    
    url.addParam("source_guid", element.getAttribute('data-guid'));
    url.requestJSON(function(status) {
      element.src = ExchangeSource.status_images[status.reachable];
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