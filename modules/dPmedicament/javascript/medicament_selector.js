// $Id: $

var MedSelector = {
  sForm     : null,
  sView     : null,
  oUrl      : null,
  selfClose : true,
  options : {
    width : 300,
    height: 400
  },

  pop: function() {
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    this.oUrl.setModuleAction("dPmedicament", "vw_idx_recherche");
    
    this.oUrl.popup(this.options.width, this.options.height, "Medicament Selector");
  },
  
  set: function(nom) {
    var oForm = document[this.sForm];
    oForm[this.sView].value = nom;
  },
  
  // Peut être appelé sans contexte : ne pas utiliser this
  close: function() {
    CCAMSelector.oUrl.close();
  }
  
}
