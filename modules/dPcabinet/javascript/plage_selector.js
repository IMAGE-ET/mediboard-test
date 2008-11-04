// $Id: $

var PlageConsultSelector = {
  sForm            : null,
  sHeure           : null,
  sPlageconsult_id : null,
  sDate            : null,
  sDuree           : null,
  sChir_id         : null,
  options : {
    width : 800,
    height: 600
  },

  pop: function() {
    var oForm = document[this.sForm];
    var url = new Url();
    url.setModuleAction("dPcabinet", "plage_selector");
    url.addParam("chir_id", oForm[this.sChir_id].value);
    url.addParam("plageconsult_id", oForm[this.sPlageconsult_id].value);
    url.popup(this.options.width, this.options.height, "PlageConsult");
  },

  set: function(heure, id, date, freq, chir_id, chirname) {
    var oForm = document[this.sForm];
    oForm[this.sHeure].value = heure;
    oForm[this.sDate].value = date;
    oForm[this.sDuree].value = freq;
    oForm[this.sChir_id].value = chir_id;
    $V(oForm[this.sPlageconsult_id], id, true);
  }
}