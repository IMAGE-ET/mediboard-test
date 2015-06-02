// $Id: $

/** TODO: Factoriser ceci pour ne pas avoir a etendre l'objet (sinon Patient.create est ecrasé) */
Patient = Object.extend({
  modulePatient: "patients",
  view: function(patient_id) {
    new Url(this.modulePatient, "vw_full_patients", "tab").
      addParam("patient_id", patient_id).
      redirectOpener();
  },
  history: function(patient_id) {
    new Url("patients", "vw_history").
      addParam("patient_id", patient_id).
      popup(600, 500, "patient history");
  },
  print: function (patient_id) {
    new Url("patients", "print_patient").
      addParam("patient_id", patient_id).
      popup(700, 550, "Patient");
  },
  showSummary: function(patient_id) {
    var url = new Url('cabinet', 'vw_resume');
    url.addParam("patient_id", patient_id);
    url.popup(800, 500, 'Summary' + (Preferences.multi_popups_resume == '1' ? patient_id : null));
  },
  create: function(form) {
    new Url("patients", "vw_edit_patients", "tab").
      addParam("patient_id", 0).
      addParam("useVitale", $V(form.useVitale)).
      addParam("covercard", $V(form.covercard)).
      addParam("name",      $V(form.nom)).
      addParam("firstName", $V(form.prenom)).
      addParam("naissance_day",  $V(form.Date_Day)).
      addParam("naissance_month",$V(form.Date_Month)).
      addParam("naissance_year", $V(form.Date_Year)).
      redirect();
  },
  createModal: function(form, callback, onclose) {
    new Url("patients", "vw_edit_patients").
      addParam("patient_id", 0).
      addParam("useVitale", $V(form.useVitale)).
      addParam("covercard", $V(form.covercard)).
      addParam("name",      $V(form.nom)).
      addParam("firstName", $V(form.prenom)).
      addParam("naissance_day",  $V(form.Date_Day)).
      addParam("naissance_month",$V(form.Date_Month)).
      addParam("naissance_year", $V(form.Date_Year)).
      addParam("callback"  , callback).
      addParam("modal"     , 1).
      modal({width: "90%", height: "90%", onClose : onclose});
  },
  edit: function(patient_id, use_vitale) {
    new Url("patients", "vw_edit_patients", "tab").
      addParam("patient_id", patient_id).
      addParam("use_vitale", use_vitale).
      redirectOpener();
  },
  editModal: function(patient_id, use_vitale, callback, onclose) {
    new Url("patients", "vw_edit_patients").
      addParam("patient_id", patient_id).
      addParam("use_vitale", use_vitale).
      addParam("callback"  , callback).
      addParam("modal"     , 1).
      modal({width: "90%", height: "90%", onClose : onclose});
  },
  exportVcard: function(patient_id) {
    new Url("patients", "ajax_export_vcard").
      addParam("patient_id", patient_id).
      addParam("suppressHeaders", 1).
      pop(700, 550, "Patient");
  },
  openINS: function($id) {
    new Url("cda", "ajax_history_ins")
      .addParam("patient_id", $id)
      .requestModal();
  },
  doMerge: function(oForm) {
    var url = new Url();
    url.setModuleAction("system", "object_merger");
    url.addParam("objects_class", "CPatient");
    url.addParam("objects_id", $V(oForm["objects_id[]"]).join("-"));
    url.popup(800, 600, "merge_patients");
  },
  doLink: function(oForm) {
    new Url("patients", "do_link", "dosql")
      .addParam("objects_id", $V(oForm["objects_id[]"]).join("-"))
      .requestUpdate("systemMsg", {
        method: 'post'
      });
  }
}, window.Patient);