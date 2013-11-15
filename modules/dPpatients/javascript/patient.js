// $Id: $

/** TODO: Factoriser ceci pour ne pas avoir a etendre l'objet (sinon Patient.create est ecrasé) */
Patient = Object.extend({
  view: function(patient_id) {
    new Url("patients", "vw_full_patients", "tab").
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
  
  edit: function(patient_id, use_vitale) {
    new Url("patients", "vw_edit_patients", "tab").
      addParam("patient_id", patient_id).
      addParam("use_vitale", use_vitale).
      redirectOpener();
  },
  editModal: function(patient_id, use_vitale) {
    new Url("patients", "vw_edit_patients").
      addParam("patient_id", patient_id).
      addParam("use_vitale", use_vitale).
      addParam("modal", 1).
      modal({width: "90%", height: "90%"});
  },
  
  exportVcard: function(patient_id) {
    new Url("patients", "ajax_export_vcard").
      addParam("patient_id", patient_id).
      addParam("suppressHeaders", 1).
      pop(700, 550, "Patient");
  },

  openINS : function($id) {
    new Url("cda", "ajax_history_ins")
      .addParam("id_patient", $id)
      .requestModal();
  },
  
  doUnlink: function(patient_id) {
    var url = new Url("dPpatients", "do_unlink", "dosql");
    url.addParam("patient_id", patient_id);
    url.requestUpdate("systemMsg", {
      method: 'post',
      onComplete : function() {
        if (window.reloadPatient) {
          reloadPatient(patient_id);
        }
      }
    });
  }
}, window.Patient);