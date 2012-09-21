// $Id$

PatSelector = {
  sForm      : null,
  sFormEasy  : null,
  sId        : null,
  sView      : null,
  sSexe      : null,
  sName       : null,
  sFirstName : null,
  sId_easy   : null,
  sView_easy : null,
  options : {
    width : 900,
    height: 600,
    useVitale: 0
  },
  prepared : {
    id: null,
    view: null,
    sexe: null
  },
  pop: function() {
    var url = new Url("dPpatients", "pat_selector");
    url.addParam("useVitale", this.options.useVitale);
    url.addParam("name"     , this.sName);
    url.addParam("firstName", this.sFirstName);
    url.modal(this.options);
  },
  
  set: function(id, view, sexe) {
    this.prepared.id = id;
    this.prepared.view = view;
    this.prepared.sexe = sexe;
    // Lancement de l'execution du set
    window.setTimeout(window.PatSelector.doSet , 1);
  },
    
  doSet: function(){
    var oForm     = document[PatSelector.sForm];
    var oFormEasy = document[PatSelector.sFormEasy];
    
    $V(oForm[PatSelector.sId]             , PatSelector.prepared.id);
    $V(oForm[PatSelector.sView]           , PatSelector.prepared.view);
    $V(oForm[PatSelector.sSexe]           , PatSelector.prepared.sexe);
    if(oFormEasy) {
      $V(oFormEasy[PatSelector.sId_easy]  , PatSelector.prepared.id);
      $V(oFormEasy[PatSelector.sView_easy], PatSelector.prepared.view);
    }
  },
  
  init: function() {
    alert("Selecteur non initialisé");
  },
  
  cancelFastSearch: function(e) {
    if(Event.key(e) == Event.KEY_ESC){
      PatSelector.init();
    }
  }
  
};
