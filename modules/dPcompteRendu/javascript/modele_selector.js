// $Id: $

/* Modele selector
   Allows to choose a modele from a praticien or a function
*/

ModeleSelector = Class.create({
  sForm      : null,
  sView      : null,
  sModele_id : null,
  sObject_id : null,
  options : {
    width : 700,
    height: 500
  },
  
  initialize: function (sForm, sView, sModele_id, sObject_id, oDefaultOptions) {
    Object.extend(this.options, oDefaultOptions);
    
    this.sForm = sForm;
    this.sView = sView;
    this.sModele_id = sModele_id;
    this.sObject_id = sObject_id;
  },

  pop: function(object_id, object_class, praticien_id) {
    var oForm = getForm(this.sForm);
    var url = new Url();
    url.setModuleAction("dPcompteRendu", "modele_selector");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("praticien_id", praticien_id);
    url.popup(this.options.width, this.options.height, "Sélecteur de modèle");
  },

  set: function(modele_id, object_id) {
    var oForm = getForm(this.sForm);
    $V(oForm[this.sModele_id], modele_id, true);
    $V(oForm[this.sObject_id], object_id, true);
  }
});

var modeleSelector = modeleSelector || []; 