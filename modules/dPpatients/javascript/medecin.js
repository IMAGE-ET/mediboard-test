// $Id: $

Medecin = {
  form: null,
  sFormName: "editSejour",
  edit : function() {
    this.form = getForm(this.sFormName);
    var url = new Url("dPpatients", "vw_medecins");
    url.popup(700, 450, "Medecin");
  },
  
  set: function(id, view) {
    $('_adresse_par_prat').show().update('Autres : '+view);
    $V(this.form.adresse_par_prat_id, id);
    $V(this.form._correspondants_medicaux, '', false);
  }
};