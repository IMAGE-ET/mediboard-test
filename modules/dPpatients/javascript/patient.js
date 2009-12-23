// $Id: $

/** TODO: Factoriser ceci pour ne pas avoir a etendre l'objet (sinon Patient.create est ecrasé) */
Patient = Object.extend({
  view: function(patient_id) {
    new Url().
      setModuleTab("dPpatients", "vw_full_patients").
      addParam("patient_id", patient_id).
      redirectOpener();
  },
	
  history: function(patient_id) {
    new Url("dPpatients", "vw_history").
      addParam("patient_id", patient_id).
      popup(600, 500, "patient history");
  },
	
  print: function (patient_id) {
    new Url("dPpatients", "print_patient").
      addParam("patient_id", patient_id).
      popup(700, 550, "Patient");
  },
	
  edit: function(patient_id, use_vitale) {
    new Url().
      setModuleTab("dPpatients", "vw_edit_patients").
      addParam("patient_id", patient_id).
      addParam("use_vitale", use_vitale).
      redirectOpener();
  }
}, window.Patient);