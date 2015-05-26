// $Id: $

Medecin = {
  form: null,
  sFormName: "editSejour",
  edit : function(form, nom, function_id) {
    this.form = form;
    var url = new Url("patients", "vw_correspondants");
    url.addParam("dialog","1");
    url.addParam("medecin_nom", nom);
    url.addParam("medecin_function_id", function_id);
    url.requestModal("1000", "760");
  },
  
  set: function(id, view) {
    $('_adresse_par_prat').show().update('Autres : '+view);
    $V(this.form.adresse_par_prat_id, id);
    $V(this.form._correspondants_medicaux, '', false);
  },

  del: function(form) {
    if (confirm("Voulez vous vraiment supprimer ce médecin du dossier patient ?")) {
      if (form._view) {$V(form._view, '');}
      if (form.medecin_traitant) {
        if ($V(form.medecin_traitant)) {
          Control.Tabs.setTabCount("medecins", "-1");
          $V(form.medecin_traitant, '');
        }
      }
      else {
        Control.Tabs.setTabCount("medecins", "-1");
        $V(form.del, 1);
      }
    }
  },
  
  modify : function(medecin_id) {
    var url = new Url("dPpatients", "vw_correspondants");
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
  },

  duplicate : function(medecin_id, callback) {
    var url = new Url('dPpatients', 'ajax_edit_medecin');
    url.addParam('medecin_id', medecin_id);
    url.addParam('duplicate', 1);
    url.requestModal('500');
    if (!Object.isUndefined(callback)) {
      url.modalObject.observe('afterClose', function(){
        callback();
      });
    }
  },

  viewPrint : function(medecin_id) {
    var url = new Url('dPpatients', 'print_medecin');
    url.addParam('medecin_id', medecin_id);
    url.popup(700, 550);
  }

};