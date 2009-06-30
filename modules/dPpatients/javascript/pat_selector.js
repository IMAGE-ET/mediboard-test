// $Id$

var PatSelector = {
  sForm     : null,
  sFormEasy : null,
  sId       : null,
  sView     : null,
  sId_easy  : null,
  sView_easy: null,
  options : {
    width : 800,
    height: 600,
    useVitale: 0
  },
  prepared : {
    id: null,
    view : null
  },
  pop: function() {
    var url = new Url();
    url.setModuleAction("dPpatients", "pat_selector");
    url.addParam("useVitale", this.options.useVitale);
    url.popup(this.options.width, this.options.height, "Patient");
  },
  
  set: function(id, view) {
    this.prepared.id = id;
    this.prepared.view = view;
    
    // Lancement de l'execution du set
    window.setTimeout( window.PatSelector.doSet , 1);
  },
    
  doSet: function(){
    var oForm     = document[PatSelector.sForm];
    var oFormEasy = document[PatSelector.sFormEasy];
    
    $V(oForm[PatSelector.sId]             , PatSelector.prepared.id);
    $V(oForm[PatSelector.sView]           , PatSelector.prepared.view);
    if(oFormEasy) {
      $V(oFormEasy[PatSelector.sId_easy]  , PatSelector.prepared.id);
      $V(oFormEasy[PatSelector.sView_easy], PatSelector.prepared.view);
    }
  }
  
}
