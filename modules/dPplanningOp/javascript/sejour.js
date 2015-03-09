// $Id: plage_selector.js 6447 2009-06-22 08:11:48Z phenxdesign $

Sejour = {
  edit: function(sejour_id) {
    new Url("dPplanningOp", "vw_edit_sejour", "tab").
      addParam("sejour_id", sejour_id).
      redirectOpener();
  },

  admission: function(date) {
    new Url("dPadmissions", "vw_idx_admission", "tab").
      addParam("date", date).
      redirectOpener();
  },

  showSSR: function(sejour_id) {
    new Url("ssr", "vw_aed_sejour_ssr", "tab").
      addParam("sejour_id", sejour_id).
      redirectOpener();
  },

  showUrgences: function(sejour_id) {
    new Url("dPurgences", "vw_aed_rpu", "tab").
      addParam("sejour_id", sejour_id).
      redirectOpener();
  },

  showDossierSoins: function(sejour_id) {
    new Url("soins", "vw_dossier_sejour", "tab").
      addParam("sejour_id", sejour_id).
      redirectOpener();
  },

  showDossierSoinsModal: function(sejour_id) {
    var url = new Url("soins", "vw_dossier_sejour");
    url.addParam("sejour_id", sejour_id);
    url.addParam("modal", 1);
    url.modal({width: "95%", height: "95%"});
  },

  modalCallback: function() {
    document.location.reload();
  },

  editModal: function(sejour_id, callback) {
    callback = callback || this.modalCallback;
    var url = new Url("planningOp", "vw_edit_sejour", "action");
    url.addParam("sejour_id", sejour_id);
    url.addParam("dialog", 1);
    url.modal({
      width     : "95%",
      height    : "95%",
      afterClose: callback
    });
  },

  showDossierPmsi : function (sejour_id, patient_id, callback) {
    callback = callback || this.modalCallback;
    var url = new Url("dPpmsi", "vw_dossier_pmsi");
    url.addParam("sejour_id", sejour_id);
    url.addParam("patient_id", patient_id);
    url.modal({
      width     : "95%",
      height    : "95%",
      afterClose: callback
    });
  }
};
