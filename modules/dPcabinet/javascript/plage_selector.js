// $Id: $

PlageConsultSelector = {
  sForm            : null,
  sHeure           : null,
  sPlageconsult_id : null,
  sDate            : null,
  sChir_id         : null,
  sFunction_id     : null,
  sDatePlanning    : null,
  sConsultId       : null,
  multipleMode     : 0,
  options          : {},
  pages            : [],
  consultations    : {},
  sLineElementId   : null,

  modal: function() {
    var oForm = getForm(this.sForm);
    var url = new Url("dPcabinet", "plage_selector");
    url.addParam("chir_id"        , $V(oForm[this.sChir_id]));
    url.addParam("function_id"    , $V(oForm[this.sFunction_id]));
    url.addParam("plageconsult_id", $V(oForm[this.sPlageconsult_id]));
    url.addParam("multipleMode"       , this.multipleMode);
    url.addParam("_line_element_id", $V(oForm[this.sLineElementId]));
    url.addParam("consultation_id", $V(oForm[this.sConsultId]));
    if (this.sDatePlanning != null && $V(oForm[this.sDatePlanning])) {
      url.addParam("date", $V(oForm[this.sDatePlanning]));
    }
    url.modal(this.options);
  },
  checkMultiple : function() {
    Control.Modal.close();
    var oForm = getForm(window.PlageConsultSelector.sForm);
    var consults = $H(window.PlageConsultSelector.consultations);
    if (consults.size() > 0) {
      var iterator = 1;
      consults.each(function(consult) {
        var consultObj = consult[1];
        if (iterator == 1) {
          window.PlageConsultSelector.set(consultObj.heure, consultObj.plage_id, consultObj.date, consultObj.chir_id);
        }
        else {
          $V(oForm["consult_multiple"], '1');
          $V(oForm["plage_id_"+iterator], consultObj.plage_id);
          $V(oForm["date_"+iterator], consultObj.date);
          $V(oForm["heure_"+iterator], consultObj.heure);
          $V(oForm["chir_id_"+iterator], consultObj.chir_id);
          $V(oForm["_consult"+iterator], consultObj.chir_name+" "+consultObj.date+" "+consultObj.heure);
          if ($V(oForm["_consult"+iterator])) {
            $("place_reca_"+iterator).show();
          }
        }
        iterator++;
      });
    }
    //clean the array
    window.PlageConsultSelector.consultations = {};
  },

  set: function(heure, plage_id, date, chir_id) {
    var oForm = getForm(this.sForm);
    $V(oForm[this.sChir_id] , chir_id, false);
    oForm[this.sChir_id].fire("ui:change");
    if (chir_id) {
      refreshListCategorie(chir_id);
      refreshFunction(chir_id);
      $V(oForm[this.sFunction_id], '');
    }
    $V(oForm[this.sHeure]          , heure);
    $V(oForm[this.sDate]           , date);
    $V(oForm[this.sPlageconsult_id], plage_id, true);
  },

  addOrRemovePage : function(key, plage_consult_id) {
    if (this.pages[key]){
      delete this.pages[key];
    }
    else {
      this.pages[key] = plage_consult_id;
    }
  },
  resetPage : function() {
    this.pages = [];
  },

  resetConsult : function() {
    this.consultations = {};
  },
  addOrRemoveConsult : function(plage_id, date, heure, chir_id, chir_name) {
    var key = plage_id+"_"+date+"_"+heure+"_"+chir_id;
    if (this.consultations[key]){
      delete this.consultations[key];
    }
    else {
      this.consultations[key] = {date:date, heure:heure, plage_id:plage_id, chir_id:chir_id, chir_name:chir_name};
    }
  }
};