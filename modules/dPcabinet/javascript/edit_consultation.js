// $Id: $

var ListConsults = {
  target: "listConsult",
  request: null,

  init: function(consult_id, prat_id, date, vue, current_m) {
    var url = new Url("dPcabinet", "httpreq_vw_list_consult");
    url.addParam("selConsult", consult_id);
    url.addParam("prat_id", prat_id);
    url.addParam("date", date);
    url.addParam("vue2", vue);
    url.addParam("current_m", current_m);
    url.addParam("fixed_width", "1");
    this.request = url.periodicalUpdate(this.target, { frequency: 90 } );

    if (consult_id && Preferences.dPcabinet_show_program == "0") {
      this.hide();    
    }
  },
  
  hide: function() {
    this.request.stop();
    $(this.target).hide();    
  },
  
  show: function() {
    this.request.start();
    $(this.target).appear();    
  },
  
  toggle: function() {
    this[$(this.target).visible() ? "hide" : "show"](); 
  }
}
