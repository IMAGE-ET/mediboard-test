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
  target:      "exchange_data_format",
  modal:       null,

  editExchange : function (exchange_guid) {
    new Url("eai", "ajax_edit_exchange")
      .addParam("exchange_guid", exchange_guid)
      .requestModal(600, 250);
  },

  refreshExchanges : function(exchange_class, exchange_type, exchange_group_id){
    new Url("eai", "ajax_refresh_exchanges")
      .addParam("exchange_class"   , exchange_class)
      .addParam("exchange_type"    , exchange_type)
      .addParam("exchange_group_id", exchange_group_id)
      .requestUpdate("exchanges", { onComplete : function() {
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
    
    if (!Object.isArray(ExchangeDataFormat.evenements[selected])) {
      $H(ExchangeDataFormat.evenements[selected]).each(function(pair){
        var v = pair.key;
        dest.insert(new Element('option', {value: v}).update($T(mod_name+'-evt_'+selected+'-'+v)));
      });
    }
  },
  
  refreshExchangesList : function(form) {
    new Url("eai", "ajax_refresh_echanges_list")
      .addFormData(form)
      .requestUpdate("exchangesList");
    return false;
  },
  
  viewExchange : function(exchange_guid) {
    new Url("eai", "ajax_vw_exchange_details")
      .addParam("exchange_guid", exchange_guid)
      .requestModal(900, 530);
  },
  
  reprocessing : function(exchange_guid){
    new Url("eai", "ajax_reprocessing_exchange")
      .addParam("exchange_guid", exchange_guid)
      .requestUpdate("systemMsg", { onComplete:
        ExchangeDataFormat.refreshExchange.curry(exchange_guid)
    });
  },

  refreshExchange : function(exchange_guid){
    new Url("eai", "ajax_refresh_exchange")
      .addParam("exchange_guid", exchange_guid)
      .requestUpdate("exchange_"+exchange_guid);
  },
  
  treatmentExchanges : function(source_guid){
    new Url("eai", "ajax_treatment_exchanges")
      .addParam("source_guid", source_guid)
      .requestUpdate("CExchangeDataFormat-treatment_exchanges");
  },

  sendMessage : function(exchange_guid, callback){
    new Url("eai", "ajax_send_message")
      .addParam("exchange_guid", exchange_guid)
      .requestUpdate("systemMsg",  callback || ExchangeDataFormat.refreshExchange.curry(exchange_guid));
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
  },
  
  viewAll: function(form) {
    var url = new Url("eai", "ajax_view_all_exchanges");
    if (form) {
      url.addFormData(form);
    }
    url.requestUpdate("exchanges");
    return false;
  },

  doesExchangeExist : function(exchange_class, exchange_id) {
    if (exchange_id) {
      new Url('eai', 'ajax_does_exchange_exist')
        .addParam('exchange_class', exchange_class)
        .addParam('exchange_id'   , exchange_id)
        .requestJSON(
          function(id) {
            if (id) {
              ExchangeDataFormat.viewExchange(exchange_class+"-"+id);
            }
            else {
              SystemMessage.notify("<div class='error'>"+$T('CExchangeDataFormat-doesnt-exist')+"</div>");
            }
        });
    }

    return false;
  },

  defineMasterIdexMissing : function(exchange_guid){
    var url = new Url("eai", "ajax_define_master_idex_missing")
      .addParam("exchange_guid", exchange_guid)
      .requestModal(400, 150);

    ExchangeDataFormat.modal = url.modalObject;
    ExchangeDataFormat.modal.observe("afterClose", function(){
      ExchangeDataFormat.refreshExchange(exchange_guid);
    });
  }
}