/**
 * JS function Exchange Data Format EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

ExchangeDataFormat = {
  evenements : null,	
  target: "exchange_data_format",
  
  refreshExchanges : function(exchange_class_name, exchange_type, exchange_group_id){
    var url = new Url("eai", "ajax_refresh_exchanges");
    url.addParam("exchange_class_name", exchange_class_name);
    url.addParam("exchange_type"	  , exchange_type);
    url.addParam("exchange_group_id"  , exchange_group_id);
    url.requestUpdate("exchanges", { onComplete : function() {
    	if (!exchange_type) {
    	  return;
    	}
    	var form = getForm("filterExchange");
		if (form) {
		  ExchangeDataFormat.refreshExchangesList(form);
		}
	} });
  },
	
  fillSelect : function(source, dest, mod_name) {
	var selected = $V(source);  
	dest.update();
    dest.insert(new Element('option', {value: ''}).update('&mdash; Liste des événements &mdash;'));
	dest.insert(new Element('option', {value: 'inconnu'}).update($T(mod_name+'-evt-none')));
	$H(ExchangeDataFormat.evenements[selected]).each(function(pair){
	  var v = pair.key;
	  dest.insert(new Element('option', {value: v}).update($T(mod_name+'-evt_'+selected+'-'+v)));
	});
  },
  
  refreshExchangesList : function(form) {
	var url = new Url("eai", "ajax_refresh_echanges_list");
	url.addFormData(form);
	url.requestUpdate("exchangesList");
	return false;
  },
  
  viewExchange : function(exchange_guid) {
    var url = new Url("eai", "ajax_vw_exchange_details");
    url.addParam("exchange_guid", exchange_guid);
	url.requestModal(800, 500);
  },
  
  reprocessing : function(exchange_guid){
    var url = new Url("eai", "ajax_reprocessing_exchange");
    url.addParam("exchange_guid", exchange_guid);
    url.requestUpdate("systemMsg", { onComplete:
    	ExchangeDataFormat.refreshExchange.curry(exchange_guid) });
  },

  refreshExchange : function(exchange_guid){console.debug(exchange_guid);
    var url = new Url("eai", "ajax_refresh_exchange");
    url.addParam("exchange_guid", exchange_guid);
    url.requestUpdate("exchange_"+exchange_guid);
  },

  sendMessage : function(exchange_guid){
    var url = new Url("eai", "ajax_send_message");
    url.addParam("exchange_guid", exchange_guid);
    url.requestUpdate("systemMsg", { onComplete:
    	ExchangeDataFormat.refreshExchange.curry(exchange_guid) });
  },
	
  changePage : function(page) {
    $V(getForm('filterExchange').page,page);
  },
  
  hide: function() {
    $(this.target).hide();    
  },
	  
  show: function() {
    $(this.target).appear();    
  },
	  
  toggle: function() {
    this[$(this.target).visible() ? "hide" : "show"](); 
  }
}