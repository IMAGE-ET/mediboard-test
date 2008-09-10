// $Id: $

var SejourHprimSelector = {
  sForm      : null,
  sId        : null,
  sIPP       : null,
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
    url.setModuleAction("hprim21", "sejour_hprim_selector");
    url.addParam("IPP", this.sIPP);
    url.addParam("name", this.sPatNom);
    url.addParam("firstName", this.sPatPrenom);
    url.popup(this.options.width, this.options.height, "SejourHprim");
  },
  
  set: function(id) {
    this.prepared.id = id;
    
    // Lancement de l'execution du set
    window.setTimeout( window.SejourHprimSelector.doSet , 1);
  },
    
  doSet: function(){
    var oForm = document[SejourHprimSelector.sForm];
    $V(oForm[SejourHprimSelector.sId]  , SejourHprimSelector.prepared.id);
  }
  
}
