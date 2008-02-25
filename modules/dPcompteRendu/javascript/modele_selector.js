// $Id: $

var ModeleSelector = {
  sForm      : null,
  sView      : null,
  sModele_id : null,
  sObject_id : null,
  options : {
    width : 500,
    height: 400
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
    Form.Element.setValue(oForm[this.sModele_id] , modele_id);
    Form.Element.setValue(oForm[this.sObject_id] , object_id);
  }
}