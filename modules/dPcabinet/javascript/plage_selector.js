// $Id: $

var PlageConsultSelector = {
  sForm            : null,
  sHeure           : null,
  sPlageconsult_id : null,
  sDate            : null,
  sDuree           : null,
  sChir_id         : null,
  sFunction_id     : null,
  options : {
    width : 800,
    height: 600
  },

  pop: function() {
    var oForm = getForm(this.sForm);
    var url = new Url("dPcabinet", "plage_selector");
    url.addParam("chir_id", oForm[this.sChir_id].value);
    url.addParam("function_id", oForm[this.sFunction_id].value);
    url.addParam("plageconsult_id", oForm[this.sPlageconsult_id].value);
    url.popup(this.options.width, this.options.height, "PlageConsult");
  },

  set: function(heure, id, date, freq, chir_id, chirname) {
    var oForm = getForm(this.sForm);
    $V(oForm[this.sChir_id]        , chir_id);
    $V(oForm[this.sHeure]          , heure);
    $V(oForm[this.sDate]           , date);
    $V(oForm[this.sDuree]          , freq);
    $V(oForm[this.sPlageconsult_id], id, true);
  }
};