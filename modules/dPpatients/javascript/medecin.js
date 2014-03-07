// $Id: $

Medecin = {
  form: null,
  sFormName: "editSejour",
  edit : function() {
    this.form = getForm(this.sFormName);
    var url = new Url("patients", "vw_correspondants");
    url.addParam("dialog","1");
    url.requestModal("1000", "760");
  },
  
  set: function(id, view) {
    $('_adresse_par_prat').show().update('Autres : '+view);
    $V(this.form.adresse_par_prat_id, id);
    $V(this.form._correspondants_medicaux, '', false);
  },
  
  modify : function(medecin_id) {
    var url = new Url("dPpatients", "vw_medecins");
    url.addParam("medecin_id", medecin_id);
    url.redirect();
  },

  doMerge : function(sform) {
    if (sform) {
      var url = new Url();
      url.setModuleAction("system", "object_merger");
      url.addParam("objects_class", "CMedecin");
      url.addParam("objects_id", $V(getForm(sform)["objects_id[]"]).join("-"));
      url.popup(800, 600, "merge_patients");
    }
  },

  editMedecin : function(medecin_id, callback) {
    var url = new Url('dPpatients', 'ajax_edit_medecin');
    url.addParam('medecin_id', medecin_id);
    url.requestModal('500');
    if (!Object.isUndefined(callback)) {
      url.modalObject.observe('afterClose', function(){
        callback();
      });
    }
  }

};