// $Id$
var SejourHprimSelector = {
  sForm       : null,
  sId         : null,
  sIPPForm    : null,
  sIPPId      : null,
  sIPP        : null,
  sPatient_id : null,
  sPatNom     : null,
  sPatPrenom  : null,
  options : {
    width : 750,
    height: 500
  },
  prepared : {
    id    : null,
    IPPid : null
  },
  pop: function() {
    var url = new Url();
    url.setModuleAction("hprim21", "sejour_hprim_selector");
    if(this.sPatient_id) {
      url.addParam("patient_id", this.sPatient_id);
    } else {
      url.addParam("IPP"       , this.sIPP);
      url.addParam("name"      , this.sPatNom);
      url.addParam("firstName" , this.sPatPrenom);
    }
    url.popup(this.options.width, this.options.height, "SejourHprim");
  },
  
  set: function(id, IPPid) {
    this.prepared.id = id;
    if(IPPid) {
      this.prepared.IPPid = IPPid;
    }
    
    // Lancement de l'execution du set
    window.setTimeout( window.SejourHprimSelector.doSet , 1);
  },
    
  doSet: function(){
    var oFormSejour = document[SejourHprimSelector.sForm];
    $V(oFormSejour[SejourHprimSelector.sId]  , SejourHprimSelector.prepared.id);
    if(SejourHprimSelector.prepared.IPPid) {
      var oFormIPP = document[SejourHprimSelector.sIPPForm];
      $V(oFormIPP[SejourHprimSelector.sIPPId]  , SejourHprimSelector.prepared.IPPid);
    }
  }
  
}
