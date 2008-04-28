// $Id: $

/* Modele selector
   Allows to choose a modele from a praticien or a function
*/
var ModeleSelector = Class.create({
  sForm      : null,
  sView      : null,
  sModele_id : null,
  sObject_id : null,
  options : {
    width : 500,
    height: 400
  },
  
  initialize: function (sForm, sView, sModele_id, sObject_id, oDefaultOptions) {
    Object.extend(this.options, oDefaultOptions);
    
    this.sForm = sForm;
    this.sView = sView;
    this.sModele_id = sModele_id;
    this.sObject_id = sObject_id;
  },

  pop: function(object_id, object_class, praticien_id) {
    var oForm = document[this.sForm];
    var url = new Url();
    url.setModuleAction("dPcompteRendu", "modele_selector");
    url.addParam("object_id", object_id);
    url.addParam("object_class", object_class);
    url.addParam("praticien_id", praticien_id);
    url.popup(this.options.width, this.options.height, "Sélecteur de modèle");
  },

  set: function(modele_id, object_id) {
    var oForm = document[this.sForm];
    oForm[this.sModele_id].setValue(modele_id);
    oForm[this.sObject_id].setValue(object_id);
    if (oForm[this.sObject_id].onchange) { // Because setValue doesn't call onchange
      oForm[this.sObject_id].onchange(oForm[this.sObject_id]);
    }
  }
});

var modeleSelector = []; 