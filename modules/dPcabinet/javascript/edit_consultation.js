// $Id: $

ListConsults = {
  target: "listConsult",
  request: null,

  init: function(consult_id, prat_id, date, vue, current_m, frequency) {
    var url = new Url("dPcabinet", "httpreq_vw_list_consult");
    url.addParam("selConsult", consult_id);
    url.addParam("prat_id", prat_id);
    url.addParam("date", date);
    url.addParam("vue2", vue);
    url.addParam("current_m", current_m);
    url.addParam("fixed_width", "1");

    var frequency = (frequency) ? frequency : "90";
    this.request = url.periodicalUpdate(this.target, { frequency: frequency } );

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
};

Consultation = window.Consultation || {
  moduleConsult : "cabinet",
  onCloseEditModal : null,
  editRDV: function(consult_id, chir_id, plage_id) {
    var url = new Url("cabinet", "edit_planning", "tab");
    url.addParam("consultation_id", consult_id);
    if (chir_id) {
      url.addParam('chir_id', chir_id);
    }
    if (plage_id) {
      url.addParam('plageconsult_id', plage_id);
    }
    url.redirect();
  },

  editRDVModal: function(consult_id, chir_id, plage_id) {
    var url = new Url("cabinet", "edit_planning");
    url.addParam("consultation_id", consult_id);
    if (chir_id) {
      url.addParam('chir_id', chir_id);
    }
    if (plage_id) {
      url.addParam('plageconsult_id', plage_id);
    }
    url.modal({
      width: "95%",
      height: "95%"
    });
    if (Consultation.onCloseEditModal) {
      url.modalObject.observe("afterClose", Consultation.onCloseEditModal);
    }
  },
  
  edit: function(consult_id, fragment) {
    var url = new Url(this.moduleConsult, "edit_consultation", "tab");
    url.addParam("selConsult", consult_id);
    if (fragment) {
      url.setFragment(fragment);
    }
    url.redirect();
  },
  
  editModal: function (consult_id, fragment) {
    var url = new Url(this.moduleConsult, "ajax_full_consult");
    url.addParam("consult_id", consult_id);
    if (fragment) {
      url.setFragment(fragment);
    }
    url.modal({
      width: "95%",
      height: "95%",
      onClose: function() {
        if (window.refreshConsultations) {
          refreshConsultations();
        }
        if (window.refreshResume) {
          refreshResume();
        }
        if (window.TdBTamm) {
          var form = getForm("filtreTdb");
          if (form) {
            TdBTamm.initUpdateListConsults(form.praticien_id.value, form.date.value);
          }
        }
      }
    });
  },

  editModalDossierAnesth: function (consult_id, dossier_anesth_id, callback) {
    callback = callback || this.modalCallback;
    var url = new Url("cabinet", "ajax_full_consult");
    url.addParam("consult_id", consult_id);
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.modal({
      width: "95%",
      height: "95%",
      afterClose: callback
    });
  },

  modalCallback: function() {
    document.location.reload();
  },

  useModal: function() {
    this.edit    = this.editModal;
    this.editRDV = this.editRDVModal;
  }
};
