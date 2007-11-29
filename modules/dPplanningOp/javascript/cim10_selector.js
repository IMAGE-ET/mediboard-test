// $Id: $

var CIM10Selector = {
  sForm     : null,
  sView     : null,
  sChir     : null,
  sCode     : null,
  oUrl      : null,
  selfClose : true,
  options : {
    width : 600,
    height: 500
  },

  pop: function() {
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    this.oUrl.setModuleAction("dPplanningOp", "code_selector");
    
    this.oUrl.addParam("chir", oForm[this.sChir].value);
    this.oUrl.addParam("type", "cim10");
    
    this.oUrl.popup(this.options.width, this.options.height, "CIM10 Selector");
  },
  
  // Code finder
  find: function(){
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    this.oUrl.setModuleAction("dPcim10", "code_finder");
    this.oUrl.addParam("code", oForm[this.sCode].value);
    this.oUrl.popup(this.options.width, this.options.height, "CIM")
  },
  
  set: function(code) {
    var oForm = document[this.sForm];
    oForm[this.sView].value = code;
  },
  
  // Peut être appelé sans contexte : ne pas utiliser this
  close: function() {
    CIM10Selector.oUrl.close();
  }

}
