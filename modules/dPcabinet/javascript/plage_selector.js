// $Id: $

PlageConsultSelector = {
  sForm            : null,
  sHeure           : null,
  sPlageconsult_id : null,
  sDate            : null,
  sChir_id         : null,
  sFunction_id     : null,
  sDatePlanning    : null,
  sLineElementId   : null,
  options 		     : {},

  modal: function() {
    var oForm = getForm(this.sForm);
    var url = new Url("dPcabinet", "plage_selector");
    url.addParam("chir_id"        , $V(oForm[this.sChir_id]));
    url.addParam("function_id"    , $V(oForm[this.sFunction_id]));
    url.addParam("plageconsult_id", $V(oForm[this.sPlageconsult_id]));
    url.addParam("_line_element_id", $V(oForm[this.sLineElementId]));
    if (this.sDatePlanning != null && $V(oForm[this.sDatePlanning])) {
      url.addParam("date", $V(oForm[this.sDatePlanning]));
    }
    url.modal(this.options);
  },

  set: function(heure, plage_id, date, chir_id) {
    var oForm = getForm(this.sForm);
    $V(oForm[this.sChir_id]        , chir_id, false);
    oForm[this.sChir_id].fire("ui:change");
    if (chir_id) {
      refreshListCategorie(chir_id);
      refreshFunction(chir_id);
      $V(oForm[this.sFunction_id], '');
    }
    $V(oForm[this.sHeure]          , heure);
    $V(oForm[this.sDate]           , date);
    $V(oForm[this.sPlageconsult_id], plage_id, true);
  }
};