// $Id: $

var CCAMSelector = {
  sForm     : null,
  sView     : null,
  sTarif    : null,
  sClass    : null,
  sChir     : null,
  oUrl      : null,
  selfClose : true,
  options : {
    width : 700,
    height: 500
  },

  pop: function() {
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    this.oUrl.setModuleAction("dPplanningOp", "code_selector");
    
    this.oUrl.addParam("chir"        , oForm[this.sChir].value);
    this.oUrl.addParam("object_class", oForm[this.sClass].value);
    this.oUrl.addParam("type"        , "ccam");
    
    this.oUrl.popup(this.options.width, this.options.height, "CCAM Selector");
  },
  
  set: function(code,tarif) {
    var oForm = document[this.sForm];
    oForm[this.sView].value = code;
    if(this.sTarif != null){
      oForm[this.sTarif].value = tarif;
    }
  },
  
  // Peut être appelé sans contexte : ne pas utiliser this
  close: function() {
    CCAMSelector.oUrl.close();
  }
  
}
