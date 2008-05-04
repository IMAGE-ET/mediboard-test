// $Id: $

var PatHprimSelector = {
  sForm      : null,
  sId        : null,
  sPatNom    : null,
  sPatPrenom : null,
  options : {
    width : 750,
    height: 500
  },
  prepared : {
    id: null
  },
  pop: function() {
    var url = new Url();
    url.setModuleAction("hprim21", "pat_hprim_selector");
    url.addParam("name", this.sPatNom);
    url.addParam("firstName", this.sPatPrenom);
    url.popup(this.options.width, this.options.height, "PatientHprim");
  },
  
  set: function(id) {
    this.prepared.id = id;
    
    // Lancement de l'execution du set
    window.setTimeout( window.PatHprimSelector.doSet , 1);
  },
    
  doSet: function(){
    var oForm = document[PatHprimSelector.sForm];
    Form.Element.setValue(oForm[PatHprimSelector.sId]  , PatHprimSelector.prepared.id);
  }
  
}
