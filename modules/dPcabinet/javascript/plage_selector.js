// $Id: $

PlageConsultSelector = {
  is_multiple      : false,
  sForm            : null,
  sHeure           : null,
  sPlageconsult_id : null,
  sDate            : null,
  sChir_id         : null,
  sFunction_id     : null,
  sDatePlanning    : null,
  sConsultId       : null,
  multipleMode     : 0,
  multipleEdit     : 0,
  options          : {},
  pages            : [],
  consultations    : {},
  sLineElementId   : null,

    modal: function() {
      var oForm = getForm(this.sForm);
      var chir_id = $V(oForm[this.sChir_id]);
      var function_id = $V(oForm[this.sFunction_id]);
      var heure = $V(oForm[this.sHeure]);

      // no chir, no function = heavy load
      if (!chir_id && !function_id) {
        if (!confirm("Vous n'avez pas selectionné de praticien ni de cabinet, voulez-vous continuer ?")) {
          return;
        }
      }

      var url = new Url("dPcabinet", "plage_selector");
      url.addParam("chir_id"        , chir_id);
      url.addParam("function_id"    , function_id);
      url.addParam("plageconsult_id", $V(oForm[this.sPlageconsult_id]));
      url.addParam("heure"          , heure);                               // first plage prise
      url.addParam("multipleMode"       , this.multipleMode);
      url.addParam("_line_element_id", $V(oForm[this.sLineElementId]));
      url.addParam("consultation_id", $V(oForm[this.sConsultId]));
      if (this.multipleEdit) {
        url.addParam("multipleEdit", this.multipleEdit);
        url.addParam("hide_finished", 0);
      }
      if (this.sDatePlanning != null && $V(oForm[this.sDatePlanning])) {
        url.addParam("date", $V(oForm[this.sDatePlanning]));
      }
      url.modal(this.options);
    },

  updateFromSelector : function() {
    if (!this.consultations.size()) {
      console.log("error, pas de plages du selecteur");
      return;
    }

    var oForm = getForm(window.PlageConsultSelector.sForm);
    var iterator = 0;
    this.consultations.each(function(elt) {
      var consult = elt.value;
      // main consult
      if (iterator == 0) {
        window.PlageConsultSelector.set(consult.heure, consult.plage_id, consult.date, consult.chir_id, consult.is_cancelled);
      }
      // multiple
      else {
        $V(oForm["consult_multiple"], '1');
        $V(oForm["plage_id_"+iterator], consult.plage_id);
        $V(oForm["date_"+iterator], consult.date);
        $V(oForm["heure_"+iterator], consult.heure);
        $V(oForm["chir_id_"+iterator], consult.chir_id);
        $V(oForm["consult_id_"+iterator], consult.consult_id);
        $V(oForm["cancel_"+iterator], consult.is_cancelled);
        $V(oForm["rques_"+iterator], consult.rques);
        $V(oForm["_consult"+iterator], consult._chirview+" le "+DateFormat.format(new Date(consult.date), "dd/MM/yyyy")+" à "+consult.heure);
        $V(oForm["element_prescription_id_"+iterator], consult.el_prescrip_id);

        if (consult.el_prescrip_libelle) {
          $V(oForm["libelle_"+iterator], consult.el_prescrip_libelle);
        }

        if ($V(oForm["_consult"+iterator])) {
          $("place_reca_"+iterator).show();
        }
      }
      iterator++;
    });
    // close the modal (there is at least one consult)
    window.Control.Modal.close();

    // if fast RDV, I pop the patselector
    if (Preferences.choosePatientAfterDate == 1 && !$V(oForm["patient_id"]) && !oForm._pause.checked) {
      window.PatSelector.init();
    }
  },

  // classic set for mono consult
  set: function(heure, plage_id, date, chir_id, is_cancelled) {
    var oForm = getForm(this.sForm);
    $V(oForm[this.sChir_id] , chir_id, false);
    oForm[this.sChir_id].fire("ui:change");
    if (chir_id) {
      refreshListCategorie(chir_id);
      refreshFunction(chir_id);
      $V(oForm[this.sFunction_id], '');
    }
    $V(oForm.annule          , (parseInt(is_cancelled) == 1) ? 1 : 0);
    $V(oForm[this.sHeure]          , heure);
    var clean_date = (date.indexOf(" ") != -1) ? date : DateFormat.format(Date.fromDATE(date), "dd/MM/yyyy");
    $V(oForm[this.sDate]           , clean_date);
    $V(oForm[this.sPlageconsult_id], plage_id, true);
  },

  removeConsult : function(plage_id) {
    if(this.consultations[plage_id]) {
      delete this.consultations[plage_id];
    }
  }
};