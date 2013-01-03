// $Id: $

ListConsults = {
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

Consultation = {
  
  editRDV: function(consult_id) {
    var url = new Url("dPcabinet", "edit_planning");
    url.addParam("consult_id", consult_id);
    url.redirect();
  },
  
  edit: function(consult_id) {
    var url = new Url("dPcabinet", "edit_consultation", "tab");
    url.addParam("selConsult", consult_id);
    url.redirect();
  },
  
  editModal: function (consult_id) {
    var url = new Url("dPcabinet", "ajax_full_consult");
    url.addParam("consult_id", consult_id);
    url.modal();
  },
  
  useModal: function() {
    this.edit = this.editModal;
  },
  
  test: function() {
    alert('toto');
  }
  
}
