// $Id: $

var MedSelector = {
  sForm     : null,
  sView     : null,
  sSearch   : null,
  oUrl      : null,
  selfClose : true,
  options : {
    width : 300,
    height: 400
  },

  pop: function() {
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    if(this.sSearch) {
      this.oUrl.addParam("produit", this.sSearch);
    }
    this.oUrl.setModuleAction("dPmedicament", "vw_idx_recherche");
    
    this.oUrl.popup(this.options.width, this.options.height, "Medicament Selector");
  },
  
  set: function(nom, code) {
    var oForm = document[this.sForm];
    oForm[this.sView].value = nom;
  },
  
  // Peut être appelé sans contexte : ne pas utiliser this
  close: function() {
    MedSelector.oUrl.close();
  }
  
}
